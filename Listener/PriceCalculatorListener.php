<?php

namespace Leaphly\CartBundle\Listener;

use Leaphly\CartBundle\Event\CartEvent;
use Leaphly\CartBundle\Calculator\PriceCalculatorInterface;

/**
 * Listener that calculate the price when Cart or Item are modified.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class PriceCalculatorListener
{
    /**
     * @var string
     */
    private $class;
    /**
     * @var \Leaphly\CartBundle\Calculator\PriceCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @param PriceCalculatorInterface $priceCalculator
     * @param string $class
     */
    public function __construct(PriceCalculatorInterface $priceCalculator, $class = 'Leaphly\CartBundle\Model\CartInterface')
    {
        $this->class = $class;
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * Calculate the cart price.
     *
     * @param CartEvent $event
     */
    public function calculatePrice(CartEvent $event)
    {
        $this->priceCalculator->calculatePrice($event->getCart());
    }
}
