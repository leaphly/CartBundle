<?php

namespace Leaphly\CartBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Leaphly\Cart\Exception\InvalidFormException;
use Leaphly\Cart\Handler\CartItemHandlerInterface;

/**
 * REST controller managing Item CRUD
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CartItemsController extends BaseController
{
    protected $itemHandler;

    public function setItemHandler(CartItemHandlerInterface $itemHandler)
    {
        $this->itemHandler = $itemHandler;
    }

    /**
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param mixed $cart_id
     *
     * @return Response
     */
    public function postItemAction($cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);
        $headers = array('Location' =>
        $this->container->get('router')->generate(
            'api_1_get_cart', array('cart_id' => $cart->getId(), '_format' => $this->container->get('request')->get('_format') ),
            true)
        );

        try{
            $this->itemHandler
                ->postItem(
                    $cart,
                    $this->container->get('request')->request->all()
                );

            $view = $this->view($cart, 201, $headers)
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');

        } catch (InvalidFormException $exception){

            return $this->view(array('errors' => $exception->getData()), 422, $headers);

        } catch (BadRequestHttpException $ex) {

            $view = $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');

        }

        return $view;
    }

    /**
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param mixed $cart_id
     * @param mixed $item_id
     *
     * @return Response
     */
    public function patchItemAction($cart_id, $item_id)
    {
        $cart = $this->fetchCartOr404($cart_id);
        $item = $cart->getItemById($item_id);

        $headers = array(
            'Location' => $this->container->get('router')->generate(
                'api_1_get_cart', array(
                    'cart_id' => $cart->getId(),
                    '_format' => $this->container->get('request')->get('_format')
                ), true
            )
        );

        try{
            $cart = $this->itemHandler
                ->patchItem(
                    $cart,
                    $item,
                    $this->container->get('request')->request->all()
                );

            $view = $this->view($cart, 200, $headers)
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');

        } catch (InvalidFormException $exception){

            return $this->view(array('errors' => $exception->getData()), 422);

        } catch (BadRequestHttpException $ex) {

            $view = $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');

        }

        return $view;
    }



    /**
     * Delete Item.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param mixed $cart_id
     * @param mixed $item_id
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteItemAction($cart_id, $item_id)
    {
        $cart = $this->fetchCartOr404($cart_id);
        $item = $cart->getItemById($item_id);

        if (!$item) {
           throw new NotFoundHttpException();
        }

        try {
        $this->itemHandler->deleteItem($cart, $item);

        $view = $this->view($cart, 200)
            ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
            ->setTemplateVar('cart');

        } catch (BadRequestHttpException $ex) {

            $view = $this->view($cart, $ex->getCode())
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');
        }

        return $view;
    } // "delete_cart_item" [DELETE] /carts/{cart_id}/items/{item_id}


    /**
     * Delete all Items.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @api
     *
     * @param mixed $cart_id
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteItemsAction($cart_id)
    {
        $cart = $this->fetchCartOr404($cart_id);

        try {
            $this->itemHandler->deleteAllItems($cart);

            $view = $this->view($cart, 200)
                ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                ->setTemplateVar('cart');
        } catch (BadRequestHttpException $ex) {

                $view = $this->view($cart, $ex->getCode())
                    ->setTemplate("LeaphlyCartBundle:Carts:getCart.html.twig")
                    ->setTemplateVar('cart');
            }

        return $view;
    }
}
