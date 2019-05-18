<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;

class Dish1FormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('dish', 'Dish');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('caption', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Название', 'required' => true))
                ->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array('label' => 'Текст описания (рецепт)', 'required' => false))
                ->add('timeeats', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array('label' => 'Приемы пищи (Ctrl)', 'required' => true,
                    'class' => 'AppBundle\Entity\TimeEat',
                    'property' => 'caption',
                    'multiple' => true,
                    'expanded' => true
                ))
                ->add('active', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Активен?', 'required' => false))
                ->add('timecook', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', array('label' => 'Время готови (мин)', 'required' => true));
    }
} 