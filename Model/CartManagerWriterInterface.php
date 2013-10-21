<?php

namespace Leaphly\CartBundle\Model;

/**
 * Interface to be implemented by cart managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to carts should happen through this interface.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface CartManagerWriterInterface
{
    /**
     * Creates an empty cart instance.
     *
     * @return CartInterface
     */
    public function createCart();

    /**
     * Deletes a cart.
     *
     * @param CartInterface $cart
     *
     * @return void
     */
    public function deleteCart(CartInterface $cart);

    /**
     * Updates a cart.
     *
     * @param CartInterface $cart
     * @param Boolean       $andFlush
     *
     * @return void
     */
    public function updateCart(CartInterface $cart, $andFlush = true);
    
    /**
     * Add an item to cart.
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return void
     */
    public function addItem(CartInterface $cart, ItemInterface $item);

    /**
     * Remove the given item to a cart.
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return boolean
     */
    public function removeItem(CartInterface $cart, ItemInterface $item);
}
