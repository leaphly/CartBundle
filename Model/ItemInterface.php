<?php

namespace Leaphly\CartBundle\Model;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface ItemInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param float $price
     */
    public function setPrice($price);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param float $finalPrice
     */
    public function setFinalPrice($finalPrice);

    /**
     * @return float
     */
    public function getFinalPrice();

}
