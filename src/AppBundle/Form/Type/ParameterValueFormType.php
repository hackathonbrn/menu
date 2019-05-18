<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class ParameterValueFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('parametervalue', 'ParameterValue');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Значение', 'required' => true))
                ->add('active', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Активно?', 'required' => false));
    }
} 