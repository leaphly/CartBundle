<?php

namespace Leaphly\CartBundle\Model;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Abstract Cart Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
abstract class CartManager implements CartManagerInterface
{
    /**
     * Returns an empty cart instance.
     *
     * @return CartInterface
     */
    public function createCart()
    {
        $class = $this->getClass();
        $cart = new $class;

        return $cart;
    }

    /**
     * Finds a Cart by its identifier or raise the 404 exception.
     *
     * @param $identifier
     *
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findOr404($identifier)
    {
        $cart = $this->find($identifier);
        if (!$cart) {
            throw new NotFoundHttpException($identifier);
        }

        return $cart;
    }

    /**
     * Remove the item from a Cart
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return Boolean
     */
    public function removeItem(CartInterface $cart, ItemInterface $item)
    {
        $return = $cart->removeItem($item);
        $this->updateCart($cart);
        return $return;
    }

    /**
     * Add the item into the Cart
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return Boolean
     */
    public function addItem(CartInterface $cart, ItemInterface $item)
    {
        $return = $cart->addItem($item);

        return $return;
    }
}
