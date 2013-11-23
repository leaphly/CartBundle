<?php

namespace Leaphly\CartBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;

use Leaphly\Cart\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * REST controller managing Cart CRUD
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CartsController extends BaseController
{
    /**
     * Get a single cart.
     *
     * @ApiDoc(
     *   resource = true,
     *   output = "Acme\Cart\Model\CartInterface",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the cart is not found"
     *   }
     * )
     * @api
     * @Annotations\View(templateVar="cart")
     *
     * @param Request $request the request object
     * @param int     $cart_id the cart id
     *
     * @return array
     *
     * @throws NotFoundHttpException when cart not exist
     */
    public function getCartAction(Request $request, $cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        return $cart;
    }

    /**
     * Removes a cart.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     400="Returned when request is a bad request",
     *     404="Returned when the cart is not found"
     *   }
     * )
     *
     * @api
     * @Annotations\View(statusCode = Codes::HTTP_NO_CONTENT)
     *
     * @param Request $request the request object
     * @param int     $cart_id the cart id
     *
     * @return array|View
     *
     * @throws NotFoundHttpException when cart not exist
     */
    public function deleteCartAction(Request $request, $cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        try {
            $this->cartHandler->deleteCart($cart);
        } catch (BadRequestHttpException $ex) {

            return $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');
        }

        return array();
    }

    /**
     * Create a new cart from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Leaphly\Cart\Form\Type\CartFormType",
     *   statusCodes = {
     *     201 = "Returned when successful created",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template = "AcmeCartBundle:Cart:newCart.html.twig",
     *   statusCode = Codes::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|RouteRedirectView
     */
    public function postCartAction(Request $request)
    {
        try {
            $cart = $this->cartHandler->postCart($request->request->all());
        } catch (InvalidFormException $exception) {

            return array('form' => $exception->getForm());
        }

        $routeOptions = array(
            'cart_id' => $cart->getId(),
            '_format' =>  $request->get('_format')
        );

        return $this->routeRedirectView('api_1_get_cart', $routeOptions, Codes::HTTP_CREATED);
    }

    /**
     * Update existing cart from the submitted data or create a new cart at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Leaphly\Cart\Form\Type\CartFormType",
     *   statusCodes = {
     *     201 = "Returned when creates a new cart",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *   }
     * )
     *
     * @Annotations\View(
     *   template="AcmeDemoBundle:Cart:editCart.html.twig",
     *   statusCode = Codes::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     * @param int     $cart_id the cart id
     *
     * @return FormTypeInterface[]|RouteRedirectView
     */
    public function putCartAction(Request $request, $cart_id)
    {
        try {
            if (!($cart = $this->cartHandler->getCart($cart_id))) {
                $cart = $this->cartHandler->postCart($request->request->all());
                $statusCode = Codes::HTTP_CREATED;
            } else {
                $cart = $this->cartHandler->putCart($cart, $request->request->all());
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
        } catch (InvalidFormException $exception) {

            return array('form' => $exception->getForm());
        }

        $routeOptions = array(
            'cart_id' => $cart->getId(),
            '_format' =>  $request->get('_format')
        );

        return $this->routeRedirectView('api_1_get_cart', $routeOptions, $statusCode);
    }

    /**
     * Partially Update existing cart from the submitted data or create a new cart at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Leaphly\Cart\Form\Type\CartFormType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *   }
     * )
     *
     * @Annotations\View(
     *   template="AcmeDemoBundle:Cart:editCart.html.twig",
     *   statusCode = Codes::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     * @param int     $cart_id the cart id
     *
     * @return FormTypeInterface[]|RouteRedirectView
     */
    public function patchCartAction(Request $request, $cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        try {
            $this->cartHandler->patchCart($cart, $request->request->all());
        } catch (InvalidFormException $exception) {

            return array('form' => $exception->getForm());
        }

        $routeOptions = array(
            'cart_id' => $cart->getId(),
            '_format' =>  $request->get('_format')
        );

        return $this->routeRedirectView('api_1_get_cart', $routeOptions, Codes::HTTP_NO_CONTENT);
    }
}