<?php

namespace Leaphly\CartBundle\Calculator;

use Leaphly\CartBundle\Model\CartInterface;

/**
 * This Calculate the prices given a cart.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class PriceCalculator implements PriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculatePrice(CartInterface $cart)
    {
        $price = 0;
        $finalPrice = 0;

        foreach ($cart->getItems() as $item) {
            $price = bcadd($price, $item->getPrice(), 2);
            $finalPrice = bcadd($finalPrice, $item->getFinalPrice(), 2);
        }

        $cart->setPrice($price);
        $cart->setFinalPrice($finalPrice);

        return $cart;
    }
}
