<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;

class Ingridient1FormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('ingridient', 'Ingridient');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('caption', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Название', 'required' => true))
            ->add('cal', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array('label' => 'Калории', 'required' => true, 'scale'=>2))
            ->add('protein', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array('label' => 'Белки', 'required' => true))
            ->add('fat', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array('label' => 'Жиры', 'required' => true))
            ->add('carbo', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array('label' => 'Углеводы', 'required' => true))
            ->add('price', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array('label' => 'Цена за 100г, руб.', 'required' => true));
    }
} 