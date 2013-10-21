<?php

namespace Leaphly\CartBundle;

/**
 * Contains all events thrown in the LeaphlyCartBundle
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
final class LeaphlyCartEvents
{
    /**
     * The ITEM_CREATE_SUCCESS event occurs when the item creation form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a \Leaphly\CartBundle\Event\ItemEvent instance.
     */
    const ITEM_CREATE_SUCCESS = 'leaphly_cart.item.create.success';

    /**
     * The ITEM_CREATE_COMPLETED event occurs after saving the item in the item creation process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a \Leaphly\CartBundle\Event\ItemEvent instance.
     */
    const ITEM_CREATE_COMPLETED = 'leaphly_cart.item.create.completed';

    /**
     * The ITEM_DELETE_COMPLETED event occurs after deleting the item.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a \Leaphly\CartBundle\Event\ItemEvent instance.
     */
    const ITEM_DELETE_COMPLETED = 'leaphly_cart.item.delete.completed';

    /**
     * The CART_CREATE_INITIALIZE event occurs when the cart creation process is initialized.
     *
     * This event allows you to modify the default values of the cart before binding the form.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_CREATE_INITIALIZE = 'leaphly_cart.cart.create.initialize';

    /**
     * The CART_CREATE_SUCCESS event occurs when the cart creation form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_CREATE_SUCCESS = 'leaphly_cart.cart.create.success';

    /**
     * The CART_CREATE_COMPLETED event occurs after saving the cart in the cart creation process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_CREATE_COMPLETED = 'leaphly_cart.cart.create.completed';

    /**
     * The CART_DELETE_COMPLETED event occurs after deleting the cart.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_DELETE_COMPLETED = 'leaphly_cart.cart.delete.completed';

    /**
     * The CART_EDIT_INITIALIZE event occurs when the profile editing process is initialized.
     *
     * This event allows you to modify the default values of the cart before binding the form.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_EDIT_INITIALIZE = 'leaphly_cart.cart.edit.initialize';

    /**
     * The CART_EDIT_SUCCESS event occurs when the profile edit form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_EDIT_SUCCESS = 'leaphly_cart.cart.edit.success';

    /**
     * The CART_EDIT_COMPLETED event occurs after saving the cart in the profile edit process.
     *
     * This event allows you to access the response which will be sent.
     * The event listener method receives a \Leaphly\CartBundle\Event\CartEvent instance.
     */
    const CART_EDIT_COMPLETED = 'leaphly_cart.cart.edit.completed';
}
