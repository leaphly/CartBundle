<?php

namespace Leaphly\CartBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\View\RedirectView;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Leaphly\Cart\Model\CartInterface;
use Leaphly\Cart\Handler\CartHandlerInterface;
use FOS\RestBundle\Util\Codes;

/**
 * BaseController adds some helpers to FOSRestController
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
abstract class BaseController extends ContainerAware
{
    protected $cartHandler;

    public function setCartHandler(CartHandlerInterface $cartHandler)
    {
        $this->cartHandler = $cartHandler;
    }
    /**
     * Create a view
     *
     * Convenience method to allow for a fluent interface.
     *
     * @param mixed   $data
     * @param integer $statusCode
     * @param array   $headers
     *
     * @return View
     */
    protected function view($data = null, $statusCode = null, array $headers = array())
    {
        return View::create($data, $statusCode, $headers);
    }

    /**
     * Create a Redirect view
     *
     * Convenience method to allow for a fluent interface.
     *
     * @param string  $url
     * @param integer $statusCode
     * @param array   $headers
     *
     * @return View
     */
    protected function redirectView($url, $statusCode = Codes::HTTP_FOUND, array $headers = array())
    {
        return RedirectView::create($url, $statusCode, $headers);
    }

    /**
     * Create a Route Redirect View
     *
     * Convenience method to allow for a fluent interface.
     *
     * @param string  $route
     * @param mixed   $parameters
     * @param integer $statusCode
     * @param array   $headers
     *
     * @return View
     */
    protected function routeRedirectView($route, array $parameters = array(), $statusCode = Codes::HTTP_CREATED, array $headers = array())
    {
        return RouteRedirectView::create($route, $parameters, $statusCode, $headers);
    }

    /**
     * Convert view into a response object.
     *
     * Not necessary to use, if you are using the "ViewResponseListener", which
     * does this conversion automatically in kernel event "onKernelView".
     *
     * @param View $view
     *
     * @return Response
     */
    protected function handleView(View $view)
    {
        return $this->container->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Create the Response.
     *
     * @param CartInterface $cart
     * @param int           $statusCode
     * @param string        $getCartRoute
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function createResponse(CartInterface $cart, $statusCode = 204, $getCartRoute = 'api_1_get_cart')
    {
        $headers = array();

        $response = new Response();
        $response->setStatusCode($statusCode);

        // set the `Location` header only when creating new resources
        if (201 === $statusCode) {
            $headers = array('Location'=>
                $this->container->get('router')->generate(
                    $getCartRoute, array('cart_id' => $cart->getId(), '_format' => $this->container->get('request')->get('_format') ),
                    true
                )
            );
        }

        return $this->view($cart, $statusCode, $headers);

    }

    /**
     * Fetch the Cart.
     *
     * @param mixed $cart_id
     *
     * @return CartInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function fetchCartOr404($cart_id)
    {
        if (!($cart = $this->cartHandler->getCart($cart_id))) {
            throw new NotFoundHttpException($cart_id);
        }

        return $cart;
    }
}
