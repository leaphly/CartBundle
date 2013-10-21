<?php

namespace Leaphly\CartBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

/**
 * Base InvalidFormException for the Form component.
 *
 * @author Simone Di Maulo <toretto460@gmail.com>
 */
class InvalidFormException extends NotAcceptableHttpException implements ExceptionInterface
{
    /**
     * @var array|null
     */
    protected $data;

    /**
     * @param string $message
     * @param array|null $data
     */
    public function __construct($message, $data = null)
    {
        parent::__construct($message);
        $this->data = $data;
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }
}
