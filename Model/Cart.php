<?php

namespace Leaphly\CartBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Finite\StatefulInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * Storage agnostic cart object
 *
 * @author Simone Di Maulo <toretto460@gmail.com>
 */
abstract class Cart implements CartInterface, StatefulInterface
{
     /**
      * @var string $id
      * @Expose
      * @Type("string")
      */
     protected $id;

     /**
      * @var String $identifier
      * @Expose
      * @Type("string")
      */
     protected $identifier;

     /**
      * @var \DateTime $expiresAt
      * @Expose
      * @Type("datetime")
      * @SerializedName("expiresAt")
      */
     protected $expiresAt;

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
      * @var ArrayCollection $items
      * @Expose
      * @Type("array<Leaphly\CartBundle\Model\Item>")
      */
     protected $items;

     /**
      * @var string $currency
      * @Expose
      * @Type("string")
      */
     protected $currency;

     /**
      * @var int $state
      * @Expose
      */
     protected $state;

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

    /**
     * Returns the cart unique id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param String $identifier
     * @return mixed|void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return String
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $date
     *
     * @return Cart
     */
    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Gets all cart items.
     *
     * @return ItemInterface[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items ?: $this->items = new ArrayCollection();
    }

    /**
     *
     * @param $itemId
     *
     * @return ItemInterface|null
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItems() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param mixed $itemId
     *
     * @return Boolean
     */
    public function hasItem($itemId)
    {
        return (!is_null($this->getItemById($itemId)));
    }

    /**
     * @param ItemInterface $item
     * @return CartInterface
     */
    public function addItem(ItemInterface $item)
    {
        if (!$this->getItems()->contains($item)) {
            $this->getItems()->add($item);
        }

        return $this;
    }

    /**
     * @param ItemInterface $item
     *
     * @return boolean
     */
    public function removeItem(ItemInterface $item)
    {
        return $this->getItems()->removeElement($item);
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

     /**
      * @return int
      */
     public function getQuantity()
     {
         return $this->getItems()->count();
     }

     /**
      * @param int $state
      */
     public function setState($state)
     {
         $this->state = $state;
     }

     /**
      * @return int
      */
     public function getState()
     {
         return $this->state;
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

    // ********************************
    // Finite State machine Interface
    // ********************************

    /**
     * @param int $state
     */
    public function setFiniteState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getFiniteState()
    {
        return $this->state;
    }

 }
