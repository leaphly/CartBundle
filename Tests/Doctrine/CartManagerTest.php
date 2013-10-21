<?php

namespace Leaphly\CartBundle\Tests\Doctrine;

use Leaphly\CartBundle\Doctrine\CartManager;
use Leaphly\CartBundle\Model\Cart;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\CartBundle\Tests\Doctrine
 */
class CartManagerTest extends \PHPUnit_Framework_TestCase
{
    const USER_CLASS = 'Leaphly\CartBundle\Tests\Doctrine\DummyCart';

    /** @var CartManager */
    protected $cartManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::USER_CLASS));

        $this->cartManager = $this->createCartManager($this->om, static::USER_CLASS);
    }

    public function testDeleteCart()
    {
        $cart = $this->getCart();
        $this->om->expects($this->once())->method('remove')->with($this->equalTo($cart));
        $this->om->expects($this->once())->method('flush');

        $this->cartManager->deleteCart($cart);
    }

    public function testGetClass()
    {
        $this->assertEquals(static::USER_CLASS, $this->cartManager->getClass());
    }

    public function testFindCartBy()
    {
        $crit = array("foo" => "bar");
        $this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->will($this->returnValue(array()));

        $this->cartManager->findCartBy($crit);
    }

    public function testFindCarts()
    {
        $this->repository->expects($this->once())->method('findAll')->will($this->returnValue(array()));

        $this->cartManager->findCarts();
    }

    public function testUpdateCart()
    {
        $cart = $this->getCart();
        $this->om->expects($this->once())->method('persist')->with($this->equalTo($cart));
        $this->om->expects($this->once())->method('flush');

        $this->cartManager->updateCart($cart);
    }

    protected function createCartManager($objectManager, $cartClass)
    {
        return new CartManager($objectManager, $cartClass);
    }

    protected function getCart()
    {
        $cartClass = static::USER_CLASS;

        return new $cartClass();
    }
}

class DummyCart extends Cart
{
    public function serialize()
    {
        return 'dummy_cart';
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->expireseAt,
            $this->id
            ) = $data;
    }
}
