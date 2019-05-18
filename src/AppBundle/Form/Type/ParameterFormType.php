<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class ParameterFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('parameter', 'Parameter');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('caption', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Название', 'required' => true))
            ->add('priority', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', array('label' => 'Приоритет показов', 'required' => false))
            ->add('fordish', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Для блюд', 'required' => false))
            ->add('foringridient', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Для ингридиентов', 'required' => false))
            ->add('active', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Активна?', 'required' => false))
            ->add('visible', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Отображать на форме подбора?', 'required' => false))
            ->add('visiblecaptin', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Отображаемый текст на фильтре', 'required' => false));
    }
} 