<?php

namespace Leaphly\CartBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;
use Acme\CartBundle\Form\CartType;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class FormFactory implements FactoryInterface
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $validationGroups;

    /**
     * @param FormFactoryInterface $formFactory
     * @param $name
     * @param $type
     * @param array $validationGroups
     */
    public function __construct(FormFactoryInterface $formFactory, $name, $type, array $validationGroups = null)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
        $this->class = '';
        $this->validationGroups = $validationGroups;
    }

    /**
     * @param array $options
     * @param null $data
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm($options = array(), $data = null)
    {
        return $this->formFactory->createNamed(
            $this->name,
            $this->type,
            $data,
            array_merge(
                $options,
                array('validation_groups' => $this->validationGroups)
            )
        );

    }
}
