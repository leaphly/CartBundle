<?php

namespace Leaphly\CartBundle\Handler;

use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\ItemInterface;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 *
 * @api
 */
Interface CartItemHandlerInterface extends ItemHandlerInterface
{
    /**
     * @param CartInterface $cart
     *
     * @return CartInterface
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @api
     */
    public function deleteAllItems(CartInterface $cart);
}
