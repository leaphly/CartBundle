<?php

namespace Leaphly\CartBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

/**
 * Transition Http exception, with status code 406
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class TransitionException extends NotAcceptableHttpException implements ExceptionInterface
{
    public function __construct($transition, $state)
    {
        parent::__construct(sprintf('Impossible to perform  transition `%s` from the state `%s`', $transition, $state));
    }
}
