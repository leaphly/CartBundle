<?php

namespace Leaphly\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class ItemFormType extends AbstractType
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $class The Item class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $dateToStringDataTransformer = new DateTimeToStringTransformer(null, null, 'Y-m-d\TH:i:sP');
        $builder
            ->add('id')
            ->add('name')
            ->add(
                $builder->create(
                    'createdAt', 'datetime'
                )
                    ->addModelTransformer($dateToStringDataTransformer)
            )
            ->add(
                $builder->create(
                    'updatedAt', 'datetime'
                )
                    ->addModelTransformer($dateToStringDataTransformer)
            )
            ->add('price')
            ->add('finalPrice')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leaphly_cart_item';
    }
}
