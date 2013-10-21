<?php

namespace Leaphly\CartBundle\Model;

/**
 * Interface to be implemented by cart managers. This adds an additional level
 * of abstraction between your application, and the actual repository
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface CartManagerReaderInterface
{
    /**
     * Finds a Cart by its identifier or raise the 404 exception.
     *
     * @param $identifier
     *
     * @return mixed
     *
     * @since  dev-Cart
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findOr404($identifier);

    /**
     * Finds a document by its identifier
     *
     * @param $identifier
     *
     * @since  dev-Cart
     *
     * @return CartInterface
     */
    public function find($identifier);

    /**
     * Finds one cart by the given criteria.
     *
     * @param array $criteria
     *
     * @return CartInterface
     */
    public function findCartBy(array $criteria);


    /**
     * Returns the cart's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

}
