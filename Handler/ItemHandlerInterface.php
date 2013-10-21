<?php

namespace Leaphly\CartBundle\Handler;

use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\ItemInterface;

/**
 * Interface that the ProductHandler must respect
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
Interface ItemHandlerInterface
{
    /**
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return ItemInterface
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     *
     * @api
     */
    public function postItem(CartInterface $cart, array $parameters);


    /**
     * @param CartInterface $cart
     * @param ItemInterface $item
     * @param array         $parameters
     *
     * @return ItemInterface
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     *
     * @api
     */
    public function patchItem(CartInterface $cart, ItemInterface $item, array $parameters);

    /**
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return CartInterface
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @api
     */
    public function deleteItem(CartInterface $cart, ItemInterface $item);


}
