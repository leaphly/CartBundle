<?php

namespace Leaphly\CartBundle\Model;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @author Simone Di Maulo <toretto460@gmail.com>
 */
abstract class Item implements ItemInterface
{
    /**
     * @var string $id
     * @Expose
     * @Type("string")
     */
    protected $id;

    /**
     * @var string $name
     * @Expose
     * @Type("string")
     */
    protected $name;

    /**
     * @var \DateTime $createdAt
     * @Expose
     * @Type("datetime")
     * @SerializedName("createdAt")
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     * @Expose
     * @Type("datetime")
     * @SerializedName("updatedAt")
     */
    protected $updatedAt;

    /**
     * @var string $currency
     * @Expose
     * @Type("string")
     */
    protected $currency;

    /**
     * @var float $price
     * @Expose
     * @Type("float")
     */
    protected $price;

    /**
     * @var float $finalPrice
     * @Expose
     * @Type("float")
     * @SerializedName("finalPrice")
     */
    protected $finalPrice;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param float $finalPrice
     */
    public function setFinalPrice($finalPrice)
    {
        $this->finalPrice = $finalPrice;
    }

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function doOnPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    public function doOnPrePersist()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        $this->setCreatedAt(new \DateTime('now'));
    }
}
