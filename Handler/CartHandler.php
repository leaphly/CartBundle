<?php

namespace Leaphly\CartBundle\Handler;

use Leaphly\CartBundle\Event\CartEventFactory;
use Leaphly\CartBundle\Exception\InvalidFormException;
use Leaphly\CartBundle\Form\Factory\FactoryInterface;
use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\CartManagerInterface;
use Leaphly\CartBundle\Transition\TransitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Leaphly\CartBundle\LeaphlyCartEvents;

/**
 * Front Handler, this is responsible for showing, deleting, patching, putting the cart.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 *
 * @api
 */
class CartHandler implements CartHandlerInterface
{
    /**
     * @var \Leaphly\CartBundle\Model\CartManagerInterface
     */
    private $cartManager;

    /**
     * @var \Leaphly\CartBundle\Form\Factory\FactoryInterface
     */
    private $formFactory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Leaphly\CartBundle\Event\CartEventFactory
     */
    protected $eventFactory;

    /**
     * @param CartManagerInterface     $cartManager
     * @param FactoryInterface         $formFactory
     * @param TransitionInterface      $finiteState
     * @param EventDispatcherInterface $dispatcher
     * @param CartEventFactory         $eventFactory
     */
    public function __construct(CartManagerInterface $cartManager,
                                FactoryInterface $formFactory,
                                TransitionInterface $finiteState,
                                EventDispatcherInterface $dispatcher,
                                CartEventFactory $eventFactory)

    {
        $this->eventFactory = $eventFactory;
        $this->dispatcher  = $dispatcher;
        $this->finiteState = $finiteState;
        $this->cartManager = $cartManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Get a cart.
     *
     * @param mixed $cartId
     *
     * @return CartInterface
     */
    public function getCart($cartId)
    {
        return $this->cartManager->find($cartId);
    }

    /**
     * Deletes a cart.
     *
     * @param CartInterface $cart
     *
     * @return $this
     */
    public function deleteCart(CartInterface $cart)
    {
        $this->finiteState->apply($cart, TransitionInterface::TRANSITION_DELETE);
        $this->cartManager->deleteCart($cart);

        $this->dispatcher->dispatch(
            LeaphlyCartEvents::CART_DELETE_COMPLETED,
            $this->eventFactory->getEvent($cart)
        );

        return $this;
    }

    /**
     * Post Cart, creates a new Cart.
     *
     * @param array $parameters
     *
     * @return CartInterface|\Symfony\Component\Form\FormInterface
     */
    public function postCart(array $parameters)
    {
        $event = $this->eventFactory->getEvent($this->cartManager->createCart(), $parameters);
        $this->dispatcher->dispatch(LeaphlyCartEvents::CART_CREATE_INITIALIZE, $event);
        $cart = $event->getCart();
        $this->applyWriteTransition($cart);

        return $this->processForm($cart, $parameters, "POST");
    }

    /**
     * Put Cart, modifies a Cart.
     *
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function putCart(CartInterface $cart, array $parameters)
    {
        $event = $this->eventFactory->getEvent($cart, $parameters);
        $this->dispatcher->dispatch(LeaphlyCartEvents::CART_EDIT_INITIALIZE, $event);
        $cart = $event->getCart();
        $this->applyWriteTransition($cart);

        return $this->processForm($cart, $parameters, "PUT");
    }

    /**
     * Patch Cart, modifies a Cart.
     *
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function patchCart(CartInterface $cart, array $parameters)
    {
        $event = $this->eventFactory->getEvent($cart, $parameters);
        $this->dispatcher->dispatch(LeaphlyCartEvents::CART_EDIT_INITIALIZE, $event);
        $cart = $event->getCart();
        $this->applyWriteTransition($cart);

        return $this->processForm($cart, $parameters, "PATCH");
    }

    /**
     * Processes the form.
     *
     * @param CartInterface $cart
     * @param array         $parameters
     * @param String        $method
     *
     * @throws \Leaphly\CartBundle\Exception\InvalidFormException
     * @return \Symfony\Component\Form\FormInterface
     */
    private function processForm(CartInterface $cart, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->createForm(array('method' => $method), $cart);
        $form->submit($parameters, false);
        if ($form->isValid()) {

            $cart = $form->getData();

            $event = $this->eventFactory->getEvent($cart, $parameters);

            $this->dispatcher->dispatch(
                $method == "POST" ? LeaphlyCartEvents::CART_CREATE_SUCCESS : LeaphlyCartEvents::CART_EDIT_SUCCESS,
                $event
            );
            $cart = $event->getCart();
            $this->cartManager->updateCart($cart);

            $this->dispatcher->dispatch(
                $method == "POST" ? LeaphlyCartEvents::CART_CREATE_COMPLETED : LeaphlyCartEvents::CART_EDIT_COMPLETED,
                $this->eventFactory->getEvent($cart)
            );

            return $cart;
        }
        throw new InvalidFormException('Invalid submitted form', $form->getErrors());
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