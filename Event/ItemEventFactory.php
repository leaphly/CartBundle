<?php

namespace Leaphly\CartBundle\Event;

use Leaphly\CartBundle\Event\ItemEvent;
use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\ItemInterface;

/**
 * Factory of ItemEvent
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
class ItemEventFactory
{
    private $class;

    public function __construct($class = '\Leaphly\CartBundle\Event\ItemEvent')
    {
        $this->class = $class;
    }

    /**
     * @param CartInterface $cart
     * @param ItemInterface $item
     * @param array         $parameters
     *
     * @return \Symfony\Component\EventDispatcher\Event
     */
    public function getEvent(CartInterface $cart, ItemInterface $item, array $parameters = null)
    {
        $class = $this->class;

        return new $class($cart, $item, $parameters);
    }
}
