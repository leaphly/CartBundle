<?php

namespace Leaphly\CartBundle\Tests;

use Leaphly\Cart\Model\Cart;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\Cart\Tests
 */
class TestCart extends Cart
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return 'dummy_cart';
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->expireseAt,
            $this->id
            ) = $data;
    }
}
