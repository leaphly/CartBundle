<?php

namespace Leaphly\CartBundle\Model;

/**
 * @author Simone Di Maulo <toretto460@gmail.com>
 */
interface CartInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return String
     */
    public function getIdentifier();

    /**
     * @param String $identifier
     * @return mixed
     */
    public function setIdentifier($identifier);

    /**
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * @param \DateTime $date
     *
     * @return CartInterface
     */
    public function setExpiresAt(\DateTime $date);

    /**
     * @param float $price
     */
    public function setPrice($price);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param float $finalPrice
     */
    public function setFinalPrice($finalPrice);

    /**
     * @return float
     */
    public function getFinalPrice();

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $state
     */
    public function setState($state);

    /**
     * @return int
     */
    public function getState();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Gets the items granted to the cart.
     *
     * @return \Traversable
     */
    public function getItems();

    /**
     * Gets the Item.
     *
     * @param $itemId
     *
     * @return self
     */
    public function getItemById($itemId);

    /**
     * Indicates whether the cart belongs to the specified item or not.
     *
     * @param mixed $itemId Identifier of the item
     *
     * @return Boolean
     */
    public function hasItem($itemId);

    /**
     * Add a item to the cart items.
     *
     * @param ItemInterface $item
     *
     * @return self
     */
    public function addItem(ItemInterface $item);

    /**
     * Remove a item from the cart items.
     *
     * @param ItemInterface $item
     *
     * @return Boolean
     */
    public function removeItem(ItemInterface $item);

}
