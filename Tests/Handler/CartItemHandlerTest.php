<?php

namespace Leaphly\CartBundle\Tests\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Leaphly\CartBundle\Event\ItemEventFactory;
use Leaphly\CartBundle\LeaphlyCartEvents;
use Leaphly\CartBundle\Handler\CartItemHandler;
use Leaphly\CartBundle\Transition\TransitionInterface;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\CartBundle\Tests\Handler
 */
class CartItemHandlerTest extends \PHPUnit_Framework_TestCase {

    protected $cartManagerWriterMock;
    protected $productFamilyProviderMock;
    protected $godFatherStrategyMock;
    protected $transitionMock;
    protected $cartItemHandler;
    protected $eventFactory;
    protected $dispatcher;

    public function setUp()
    {
        $this->cartManagerWriterMock = $this->getMock('Leaphly\CartBundle\Model\CartManagerWriterInterface');

        $this->productFamilyProviderMock = $this->getMock('Leaphly\CartBundle\Provider\ProductFamilyProviderInterface');

        $this->godFatherStrategyMock = $this->getMockBuilder('PUGX\Godfather\Godfather')
            ->disableOriginalConstructor()
            ->setMethods(array('getItemHandler'))
            ->getMock();

        $this->transitionMock = $this->getMockBuilder('Leaphly\CartBundle\Transition\TransitionInterface')
            ->disableOriginalConstructor()->getMock();

        $this->eventFactory = new ItemEventFactory();
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->cartItemHandler = new CartItemHandler(
            $this->cartManagerWriterMock,
            $this->productFamilyProviderMock,
            $this->godFatherStrategyMock,
            $this->transitionMock,
            $this->dispatcher,
            $this->eventFactory
        );
    }
    public function testPostItem()
    {
        $parameters = array('cart_id' => '2');

        $cartItemHandlerMock = $this->getMock('Leaphly\CartBundle\Handler\ItemHandlerInterface');
        $cartMock = $this->getMock('Leaphly\CartBundle\Model\CartInterface');
        $cartItemMock = $this->getMock('Leaphly\CartBundle\Model\ItemInterface');

        $this->transitionMock->expects($this->once())
            ->method('can')
            ->with($this->equalTo($cartMock), $this->equalTo(TransitionInterface::TRANSITION_CART_WRITE))
            ->will($this->returnValue(true));

        $this->transitionMock->expects($this->once())
            ->method('apply')
            ->with($this->equalTo($cartMock), $this->equalTo(TransitionInterface::TRANSITION_CART_WRITE));

        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with($this->equalTo(LeaphlyCartEvents::ITEM_CREATE_SUCCESS), $this->isInstanceOf('\Leaphly\CartBundle\Event\ItemEvent'));

        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with($this->equalTo(LeaphlyCartEvents::ITEM_CREATE_COMPLETED), $this->isInstanceOf('\Leaphly\CartBundle\Event\ItemEvent'));

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $this->productFamilyProviderMock->expects($this->once())
            ->method('getProductFamily')
            ->with($this->equalTo($parameters))
            ->will($this->returnValue('className'));

        $cartItemHandlerMock->expects($this->once())
            ->method('postItem')
            ->with($this->equalTo($cartMock), $this->equalTo($parameters))
            ->will($this->returnValue($cartItemMock));

        $this->godFatherStrategyMock->expects($this->once())
            ->method('getItemHandler')
            ->with($this->equalTo('className'))
            ->will($this->returnValue($cartItemHandlerMock));

        $this->cartManagerWriterMock->expects($this->once())
            ->method('addItem')
            ->with($this->equalTo($cartMock), $this->equalTo($cartItemMock));

        $this->cartItemHandler->postItem($cartMock, $parameters);
    }

    public function testPatchItem()
    {

        $parameters = array('cart_id' => '2');

        $cartItemHandlerMock = $this->getMock('Leaphly\CartBundle\Handler\ItemHandlerInterface');
        $cartMock = $this->getMock('Leaphly\CartBundle\Model\CartInterface');
        $cartItemMock = $this->getMock('Leaphly\CartBundle\Model\ItemInterface');

        $this->transitionMock->expects($this->once())
            ->method('can')
            ->with($this->equalTo($cartMock), $this->equalTo(TransitionInterface::TRANSITION_CART_WRITE))
            ->will($this->returnValue(true));

        $this->transitionMock->expects($this->once())
            ->method('apply')
            ->with($this->equalTo($cartMock), $this->equalTo(TransitionInterface::TRANSITION_CART_WRITE));

        $this->dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with($this->equalTo(LeaphlyCartEvents::ITEM_CREATE_SUCCESS), $this->isInstanceOf('\Leaphly\CartBundle\Event\ItemEvent'));

        $this->dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with($this->equalTo(LeaphlyCartEvents::ITEM_CREATE_COMPLETED), $this->isInstanceOf('\Leaphly\CartBundle\Event\ItemEvent'));

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch');

        $this->productFamilyProviderMock->expects($this->once())
            ->method('getProductFamily')
            ->with($this->equalTo($parameters))
            ->will($this->returnValue('className'));

        $cartItemHandlerMock->expects($this->once())
            ->method('patchItem')
            ->with($this->equalTo($cartMock), $this->equalTo($cartItemMock), $this->equalTo($parameters))
            ->will($this->returnValue($cartItemMock));

        $this->godFatherStrategyMock->expects($this->once())
            ->method('getItemHandler')
            ->with($this->equalTo('className'))
            ->will($this->returnValue($cartItemHandlerMock));

        $this->cartManagerWriterMock->expects($this->once())
            ->method('updateCart')
            ->with($this->equalTo($cartMock));


        $this->cartItemHandler->patchItem($cartMock, $cartItemMock, $parameters);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testPostItemException()
    {
        $parameters = array('cart_id' => '2');

        $cartItemHandlerMock = $this->getMock('Leaphly\CartBundle\Handler\ItemHandlerInterface');
        $cartMock = $this->getMock('Leaphly\CartBundle\Model\CartInterface');

        $this->productFamilyProviderMock->expects($this->once())
            ->method('getProductFamily')
            ->with($this->equalTo($parameters))
            ->will($this->returnValue(null));

        $cartItemHandlerMock->expects($this->never())
            ->method('postItem');

        $this->godFatherStrategyMock->expects($this->never())
            ->method('getItemHandler');

        $this->cartManagerWriterMock->expects($this->never())
            ->method('addItem');

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->cartItemHandler->postItem($cartMock, $parameters);
    }

    public function testDeleteAllItem()
    {
        $cartItemMockProductA = $this->getMock('Leaphly\CartBundle\Model\ItemInterface');
        $cartItemMockProductB = $this->getMock('Leaphly\CartBundle\Model\ItemInterface');

        $cartMock = $this->getMock('Leaphly\CartBundle\Model\CartInterface');
        $cartMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue(new ArrayCollection(array($cartItemMockProductA, $cartItemMockProductB))));

        $this->cartManagerWriterMock->expects($this->at(0))
            ->method('removeItem')
            ->with($this->equalTo($cartMock), $this->equalTo($cartItemMockProductA))
            ->will($this->returnValue(true));

        $this->cartManagerWriterMock->expects($this->at(1))
            ->method('removeItem')
            ->with($this->equalTo($cartMock), $this->equalTo($cartItemMockProductB))
            ->will($this->returnValue(true));

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->equalTo(LeaphlyCartEvents::ITEM_DELETE_COMPLETED), $this->isInstanceOf('\Leaphly\CartBundle\Event\ItemEvent'));

        $this->cartItemHandler->deleteAllItems($cartMock);
    }

    public function testDeleteCartItem()
    {
        $cartItemMockProductA = $this->getMock('Leaphly\CartBundle\Model\ItemInterface');

        $cartMock = $this->getMock('Leaphly\CartBundle\Model\CartInterface');

        $this->transitionMock->expects($this->once())
            ->method('apply')
            ->with($this->equalTo($cartMock), $this->equalTo(TransitionInterface::TRANSITION_CART_WRITE));

        $this->cartManagerWriterMock->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($cartMock), $this->equalTo($cartItemMockProductA))
            ->will($this->returnValue(true));

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(LeaphlyCartEvents::ITEM_DELETE_COMPLETED), $this->isInstanceOf('\Leaphly\CartBundle\Event\ItemEvent'));

        $this->cartItemHandler->deleteItem($cartMock, $cartItemMockProductA);
    }
}