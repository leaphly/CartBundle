<?php

namespace Leaphly\CartBundle\Handler;

use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Model\ItemInterface;
use Symfony\Component\Form\FormInterface;
use Leaphly\CartBundle\Exception\InvalidFormException;

/**
 * Base Handler for the final ProductItemHandler Class.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
abstract class ItemHandler implements ItemHandlerInterface
{
    /**
     * Creates a Form with those parameters.
     *
     * @param               $method
     * @param array         $parameters
     * @param ItemInterface $item
     *
     * @return FormInterface
     */
    abstract protected function createForm($method, array $parameters, ItemInterface $item = null);

    /**
     * Processes the form, and returns the data in the format needed for the underlying object.
     *
     * @param               $method
     * @param array         $parameters
     * @param ItemInterface $item
     *
     * @return mixed
     * @throws InvalidFormException
     */
    protected function processForm($method, array $parameters, ItemInterface $item = null)
    {
        $form = $this->createForm($method, $parameters, $item);
        $form->submit($parameters, false);

        if ($form->isValid()) {
            return $form->getData();
        }

        throw new InvalidFormException('Invalid submitted form', $form->getErrors());
    }

    /**
     * @param CartInterface $cart
     * @param ItemInterface $item
     *
     * @return Boolean
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @api
     */
    public function deleteItem(CartInterface $cart, ItemInterface $item)
    {
        return true;
    }
}
