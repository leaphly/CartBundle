<?php

namespace Leaphly\CartBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Leaphly\CartBundle\Exception\InvalidFormException;

/**
 * RESTful controller managing Cart CRUD
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CartsController extends BaseController
{
    /**
     * This Action get the cart.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a cart"
     * )
     *
     * @api
     *
     * @param $cart_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCartAction($cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        $view = $this->view($cart, 200)
            ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
            ->setTemplateVar('cart');

        return $view;
    }

    /**
     * Delete a cart.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param $cart_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCartAction($cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        try {
            
        $this->cartHandler->deleteCart($cart);

            $view = $this->view(array(), 204)
                ->setTemplate("LeaphlyShoppingCartBundle:Carts:deleteCart.html.twig");
        } catch (BadRequestHttpException $ex) {

            $view = $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');
        }

        return $view;
    }

    /**
     * Create a Cart.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @return Response
     */
    public function postCartAction()
    {
        try {
            $newCart = $this->cartHandler->postCart(
                $this->container->get('request')->request->all()
            );

            $headers = array(
                'Location' => $this->container->get('router')->generate(
                    'api_1_get_cart', array(
                        'cart_id' => $newCart->getId(),
                        '_format' => $this->container->get('request')->get('_format')
                    ), true
                )
            );

            return $this->view($newCart, 201, $headers)
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');

        } catch (InvalidFormException $exception) {

            return $this->view(array('errors' => $exception->getData()), 422);
        }
    }

    /**
     * Edit a Cart
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param $cart_id
     *
     * @return Response
     */
    public function putCartAction($cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        $headers = array(
            'Location' => $this->container->get('router')->generate(
                'api_1_get_cart', array(
                    'cart_id' => $cart->getId(),
                    '_format' => $this->container->get('request')->get('_format')
                ), true
            )
        );

        try {
            $newCart = $this->cartHandler->putCart(
                $cart, $this->container->get('request')->request->all()
            );

            return $this->view($newCart, 200, $headers);

        } catch (InvalidFormException $exception){
            return $this->view(array('errors' => $exception->getData()), 422, $headers);
        } catch (BadRequestHttpException $ex) {

            return $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');
        }
    }

    /**
     * Edit a Cart
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param $cart_id
     *
     * @return Response
     */
    public function patchCartAction($cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        $headers = array(
            'Location' => $this->container->get('router')->generate(
                'api_1_get_cart', array(
                    'cart_id' => $cart->getId(),
                    '_format' => $this->container->get('request')->get('_format')
                ), true
            )
        );

        try {
            $newCart = $this->cartHandler->patchCart(
                $cart, $this->container->get('request')->request->all()
            );

            return $this->view($newCart, 200, $headers)
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');

        } catch (InvalidFormException $exception){

            return $this->view(array('errors' => $exception->getData()), 422, $headers);

        } catch (BadRequestHttpException $ex) {

            return $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');
        }
    }
}