<?php

namespace Leaphly\CartBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * REST controller managing Transition CRUD
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CartTransitionsController extends FOSRestController
{
    /**
     * Apply a transition to a cart.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @param mixed  $cart_id
     * @param string $transition

     * @return \FOS\RestBundle\View\View
     *
     * @api
     */
    public function postTransitionAction($cart_id, $transition)
    {
        $cart = $this->get('leaphly_cart.cart_manager')->findOr404($cart_id);

        $this->get('leaphly_cart.cart.transition')->apply($cart, $transition, true);

        return $this->view($cart, 201, array())
            ->setTemplate("LeaphlyCartBundle:Carts:getTransition.html.twig")
            ->setTemplateVar('cart');
    }

    /**
     * Get the answer if is possible to apply a transaction to the cart.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @param mixed  $cart_id
     * @param string $transition

     * @return \FOS\RestBundle\View\View
     *
     * @api
     */
    public function getTransitionAction($cart_id, $transition)
    {
        $cart = $this->get('leaphly_cart.cart_manager')->findOr404($cart_id);
        $status = 200;

        if (!($content = $this->get('leaphly_cart.cart.transition')->can($cart, $transition))) {
            $status = 406;
        }

        return $this->view($content, $status, array())
            ->setTemplate("LeaphlyCartBundle:Transitions:getTransition.html.twig")
            ->setTemplateVar('content');
    }
}
