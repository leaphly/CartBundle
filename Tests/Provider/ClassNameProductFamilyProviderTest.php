<?php

namespace Leaphly\CartBundle\Tests\Provider;

use Leaphly\CartBundle\Provider\ClassNameProductFamilyProvider;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\CartBundle\Tests\Provider
 */
class ClassNameProductFamilyProviderTest extends \PHPUnit_Framework_TestCase
{
    const PRODUCT_CLASS = 'Leaphly\CartBundle\Tests\Provider\DummyProduct';

    /** @var ClassNameProductFamilyProvider */
    protected $productFamilyProvider;
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
            ->with($this->equalTo(static::PRODUCT_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::PRODUCT_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::PRODUCT_CLASS));

        $this->productFamilyProvider = $this->createProductFamilyProvider($this->om, static::PRODUCT_CLASS);
    }

    public function testGetProductFamilyWithFamilyInTheParameters()
    {
        $family = 'family';
        $parameters = array('family' => $family, 'product' => null);

        $this->assertEquals($family, $this->productFamilyProvider->getProductFamily($parameters));
    }

    public function testGetProductFamilyWithProductIdInTheParameters()
    {
        $family = null;
        $productIdToAssert = 'DummyProduct';
        $productId = 109;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($productId))
            ->will($this->returnValue(new DummyProduct()));

        $parameters = array('family' => $family, 'product' => $productId);
        $this->assertEquals($productIdToAssert, $this->productFamilyProvider->getProductFamily($parameters));
    }

    protected function createProductFamilyProvider($objectManager, $cartClass)
    {
        return new ClassNameProductFamilyProvider($objectManager, $cartClass);
    }
}

Class DummyProduct {

}