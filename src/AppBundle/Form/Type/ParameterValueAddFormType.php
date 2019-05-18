<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class ParameterValueAddFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('valueadd', 'Dish');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parameter', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array('label' => 'Характеристика', 'required' => false, 'mapped'=>false,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->where('p.fordish = :fordish')
                            ->setParameters(array('fordish' => true))
                            ->orderBy('p.caption', 'ASC');
                    },
                    'empty_value' => '- Выберите хар-ку для фильтрации значений -',
                    'class' => 'AppBundle\Entity\Parameter',
                    'property' => 'caption',
                    'multiple' => false,
                    'expanded' => false))
                ->add('value', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array('label' => 'Значение (Выбор нескольких - зажмите CTRL и выбирайте)', 'required' => false, 'mapped'=>false,
                    'empty_value' => '- Выберите значение  -',
                    'class' => 'AppBundle\Entity\ParameterValue',
                    'property' => 'value',
                    'multiple' => true,
                    'expanded' => false,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('v')
                            ->leftJoin('v.parameter', 'p')
                            ->where('p.fordish = :fordish')
                            ->setParameters(array('fordish' => true))
                            ->orderBy('v.parameter', 'ASC')
                            ->addOrderBy('v.value', 'ASC');
                    },
                    ))
                ;
    }
} 