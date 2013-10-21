<?php

namespace Leaphly\CartBundle\Handler;

use Leaphly\CartBundle\Model\CartInterface;

/**
 * Front Handler, this is responsible for showing, deleting, patching, putting the cart.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 *
 * @api
 */
interface CartHandlerInterface
{
    /**
     * Get a cart.
     *
     * @param mixed $cartId
     *
     * @return CartInterface
     *
     * @api
     */
    public function getCart($cartId);

    /**
     * Deletes a cart.
     *
     * @param CartInterface $cart
     *
     * @return $this
     *
     * @api
     */
    public function deleteCart(CartInterface $cart);

    /**
     * Post Cart, creates a new Cart.
     *
     * @param array  $parameters
     *
     * @return CartInterface|\Symfony\Component\Form\FormInterface
     */
    public function postCart(array $parameters);

    /**
     * Put Cart, modifies a Cart.
     *
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @api
     */
    public function putCart(CartInterface $cart, array $parameters);

    /**
     * Patch Cart, modifies a Cart.
     *
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @api
     */
    public function patchCart(CartInterface $cart, array $parameters);
} 