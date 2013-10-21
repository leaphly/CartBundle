<?php

namespace Leaphly\CartBundle\Form\Factory;

/*
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface FactoryInterface
{
    /**
     * @param $options
     * @param $data
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm($options, $data);
}
