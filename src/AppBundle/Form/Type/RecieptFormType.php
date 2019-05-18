<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class RecieptFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('reciept', 'Reciept');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ingridient', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array('label' => 'Ингридиент', 'required' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('i')
                    ->where('i.active = :active')
                    ->setParameters(array('active' => true))
                    ->orderBy('i.caption', 'ASC');
            },
            'empty_value' => '- Ингредиент -',
            'class' => 'AppBundle\Entity\Ingridient',
            'property' => 'caption',
            'multiple' => false,
            'expanded' => false))
        ->add('quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array('scale'=>1, 'label'=>'Количество,г', 'required'=>false));
    }
} 