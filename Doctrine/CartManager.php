<?php

namespace Leaphly\CartBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\CartManager as BaseCartManager;

/**
 * The concrete cart manager, is responsible of Reading and Writing on the Cart (not manipulation).
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CartManager extends BaseCartManager
{
    protected $objectManager;
    protected $class;
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteCart(CartInterface $cart)
    {
        $this->objectManager->remove($cart);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function find($identifier)
    {
        return $this->repository->find($identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function findCartBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findCarts()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function updateCart(CartInterface $cart, $andFlush = true)
    {
        $this->objectManager->persist($cart);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
