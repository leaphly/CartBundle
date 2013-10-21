<?php

namespace Leaphly\CartBundle\Handler;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use PUGX\Godfather\Godfather;
use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\CartManagerWriterInterface;
use Leaphly\CartBundle\Model\ItemInterface;
use Leaphly\CartBundle\Provider\ProductFamilyProviderInterface;
use Leaphly\CartBundle\Event\ItemEventFactory;
use Leaphly\CartBundle\LeaphlyCartEvents;
use Leaphly\CartBundle\Transition\TransitionInterface;

/**
 * Front Handler, this act as man-in-the-middle, calling the proper ItemHandler via strategy.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CartItemHandler implements CartItemHandlerInterface
{
    /**
     * @var \Leaphly\CartBundle\Model\CartManagerWriterInterface
     */
    protected $cartManager;

    /**
     * @var \Leaphly\CartBundle\Provider\ProductFamilyProviderInterface
     */
    protected $productFamilyProvider;

    /**
     * @var \PUGX\Godfather\Godfather
     */
    protected $strategy;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Leaphly\CartBundle\Transition\TransitionInterface
     */
    protected $finiteState;

    /**
     * @var \Leaphly\CartBundle\Event\ItemEventFactory
     */
    protected $eventFactory;

     /**
      * @param CartManagerWriterInterface     $cartManager
      * @param ProductFamilyProviderInterface $productFamilyProvider
      * @param Godfather                      $strategy
      * @param TransitionInterface            $finiteState
      * @param EventDispatcherInterface       $dispatcher
      * @param ItemEventFactory               $eventFactory
      */
    public function __construct(CartManagerWriterInterface $cartManager,
                                ProductFamilyProviderInterface $productFamilyProvider,
                                Godfather $strategy,
                                TransitionInterface $finiteState,
                                EventDispatcherInterface $dispatcher,
                                ItemEventFactory $eventFactory)
    {
        $this->eventFactory = $eventFactory;
        $this->dispatcher = $dispatcher;
        $this->cartManager = $cartManager;
        $this->productFamilyProvider = $productFamilyProvider;
        $this->strategy = $strategy;
        $this->finiteState = $finiteState;
    }

    /**
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return CartInterface
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function postItem(CartInterface $cart, array $parameters)
    {
        $this->applyWriteTransition($cart);
        $productFamily = $this->productFamilyProvider->getProductFamily($parameters);

        if (null == $productFamily) {
            throw new InvalidParameterException('Impossible to fetch product Family from Request.');
        }

        $cartItemHandler = $this->strategy->getItemHandler($productFamily);
        $item = $cartItemHandler->postItem($cart, $parameters);
        $this->cartManager->addItem($cart, $item);

        $event = $this->eventFactory->getEvent($cart, $item, $parameters);
        $this->dispatcher->dispatch(LeaphlyCartEvents::ITEM_CREATE_SUCCESS, $event);

        $item = $event->getItem();
        $cart = $event->getCart();

        $this->cartManager->updateCart($cart);
        $this->dispatcher->dispatch(LeaphlyCartEvents::ITEM_CREATE_COMPLETED, $this->eventFactory->getEvent($cart, $item));

        return $cart;
    }

    /**
     * @param CartInterface $cart
     * @param ItemInterface $item
     * @param array         $parameters
     *
     * @return CartInterface
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function patchItem(CartInterface $cart, ItemInterface $item, array $parameters)
    {
        $this->applyWriteTransition($cart);
        $productFamily = $this->productFamilyProvider->getProductFamily($parameters);

        if (null == $productFamily) {
            throw new InvalidParameterException('Impossible to fetch product Family from Request.');
        }

        $cartItemHandler = $this->strategy->getItemHandler($productFamily);
        $item = $cartItemHandler->patchItem($cart, $item, $parameters);

        $event = $this->eventFactory->getEvent($cart, $item, $parameters);
        $this->dispatcher->dispatch(LeaphlyCartEvents::ITEM_CREATE_SUCCESS, $event);

        $item = $event->getItem();
        $cart = $event->getCart();

        $this->cartManager->updateCart($cart);
        $this->dispatcher->dispatch(LeaphlyCartEvents::ITEM_CREATE_COMPLETED, $this->eventFactory->getEvent($cart, $item));

        return $cart;
    }


    /**
     * Remove the given cart item from the given cart and apply a transition
     * from current state to TRANSITION_DELETE.
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return CartInterface
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function deleteItem(CartInterface $cart, ItemInterface $item)
    {
        $this->finiteState->apply($cart, TransitionInterface::TRANSITION_CART_WRITE);
        $this->doDeleteItem($cart, $item);

        return $cart;
    }

    /**
     * Remove all cart items of the given cart and apply a transition
     * from current state to TRANSITION_DELETE.
     *
     * @param CartInterface $cart
     *
     * @return CartInterface
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function deleteAllItems(CartInterface $cart)
    {
        foreach($cart->getItems() as $item) {
            $this->doDeleteItem($cart, $item);
        }

        return $cart;
    }

    /**
     * Remove the given cart item from the given cart.
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    private function doDeleteItem(CartInterface $cart, ItemInterface $item)
    {
        if (!$this->cartManager->removeItem($cart, $item)) {
            throw new BadRequestHttpException();
        }

        $this->dispatcher->dispatch( LeaphlyCartEvents::ITEM_DELETE_COMPLETED, $this->eventFactory->getEvent($cart, $item));
    }

    /**
     * Tries to apply a Write Transition.
     *
     * @param CartInterface $cart
     *
     * @return CartInterface
     */
    private function applyWriteTransition(CartInterface $cart)
    {
        if ($this->finiteState->can($cart, TransitionInterface::TRANSITION_CART_WRITE)) {
            $this->finiteState->apply($cart, TransitionInterface::TRANSITION_CART_WRITE);
        } else {
            $this->finiteState->apply($cart, TransitionInterface::TRANSITION_ORDER_WRITE);
        }

        return $cart;
    }
}