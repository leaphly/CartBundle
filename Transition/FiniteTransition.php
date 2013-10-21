<?php

namespace Leaphly\CartBundle\Transition;

use Finite\Context;
use Leaphly\CartBundle\Model\CartInterface;
use Leaphly\CartBundle\Exception\TransitionException;
use Leaphly\CartBundle\Model\CartManagerWriterInterface;

/**
 * Provides the Transition to the Cart.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class FiniteTransition implements TransitionInterface
{
    /**
     * @var CartManagerWriterInterface
     */
    private $cartManager;
    /**
     * @var \Finite\Context
     */
    protected $finiteContext;

    /**
     * @param CartManagerWriterInterface $cartManager
     * @param Context $finiteContext
     */
    public function __construct(CartManagerWriterInterface $cartManager, Context $finiteContext)
    {
        $this->cartManager = $cartManager;
        $this->finiteContext = $finiteContext;
    }

    /**
     * {@inheritDoc}
     */
    public function can(CartInterface $cart, $transition)
    {
        $finiteStateMachine = $this->finiteContext->getStateMachine($cart);

        return $finiteStateMachine->can($transition);
    }

    /**
     * {@inheritDoc}
     */
    public function apply(CartInterface $cart, $transition, $andFlush = false)
    {
        $finiteStateMachine = $this->finiteContext->getStateMachine($cart);
        if ($finiteStateMachine->can($transition)) {
            $finiteStateMachine->apply($transition);
            if ($andFlush) {
                $this->cartManager->updateCart($cart);
            }

            return true;
        }
        throw new TransitionException($transition, $cart->getState());
    }

}
