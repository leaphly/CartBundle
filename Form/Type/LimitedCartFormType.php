<?php

namespace Leaphly\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class LimitedCartFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $cartClass;

    /**
     * @param string $cartClass
     */
    public function __construct($cartClass)
    {
        $this->cartClass = $cartClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, array('mapped' => false))
            ->add('identifier')
            ->add('items','collection', array(
                'allow_add'     => true,
                'allow_delete'  => true,
                'mapped'        => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->cartClass,
            'csrf_protection'   => false,
            'intention'  => 'consumer_edit',
       ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'consumer_cart';
    }
}
