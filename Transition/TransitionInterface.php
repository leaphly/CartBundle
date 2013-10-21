<?php

namespace Leaphly\CartBundle\Transition;

use Leaphly\CartBundle\Model\CartInterface;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 */
Interface TransitionInterface
{
    CONST TRANSITION_CART_WRITE = 'cart_write';
    CONST TRANSITION_ORDER_START = 'order_start';
    CONST TRANSITION_ORDER_WRITE = 'order_write';
    CONST TRANSITION_ORDER_SUCCESS = 'order_success';
    CONST TRANSITION_ORDER_CLOSE = 'order_close';
    CONST TRANSITION_DELETE = 'delete';

    CONST FLUSH = true;

    /**
     * Checks if the Cart can do a transition.
     *
     * @param CartInterface $cart       The cart
     * @param string        $transition The transition name
     *
     * @return Boolean      True if the Cart can do that transition.
     *
     * @api
     */
    public function can(CartInterface $cart, $transition);

    /**
     * Apply a transition to the Cart
     *
     * @param CartInterface $cart       The cart
     * @param string        $transition The transition name
     *
     * @return mixed
     *
     * @throws \Leaphly\CartBundle\Exception\TransitionException
     *
     * @api
     */
    public function apply(CartInterface $cart, $transition);

}
