<?php

namespace Leaphly\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\CartBundle\Form\Type
 */
class CartFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $cartClass;

    /**
     * @param $cartClass
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

        $dateToStringDataTransformer = new DateTimeToStringTransformer(null, null, 'Y-m-d\TH:i:sP');
        $builder
            ->add('id', null, array('mapped' => false))
            ->add('identifier')
            ->add(
                $builder->create(
                    'expiresAt', 'text'
                    )
                    ->addModelTransformer($dateToStringDataTransformer)
                )
            ->add(
                $builder->create(
                    'createdAt', 'text'
                    )
                    ->addModelTransformer($dateToStringDataTransformer)
            )
            ->add(
                $builder->create(
                    'updatedAt', 'text'
                    )
                    ->addModelTransformer($dateToStringDataTransformer)
            )
            ->add('state')
            ->add('currency','currency')
            ->add('price')
            ->add('finalPrice')
            ->add('items','collection', array(
                'allow_add'     => true,
                'allow_delete'  => true,
                'mapped'        => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->cartClass,
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leaphly_cart';
    }
}
