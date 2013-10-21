<?php

namespace Leaphly\CartBundle\Event;

use Leaphly\CartBundle\Model\CartInterface;

/**
 * Event Factory
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
class CartEventFactory
{
    private $class;

    public function __construct($class = '\Leaphly\CartBundle\Event\CartEvent')
    {
        $this->class = $class;
    }

    /**
     * @param CartInterface $cart
     * @param array         $parameters
     *
     * @return \Symfony\Component\EventDispatcher\Event
     */
    public function getEvent(CartInterface $cart, array $parameters = null)
    {
        $class = $this->class;

        return new $class($cart, $parameters);
    }
}
