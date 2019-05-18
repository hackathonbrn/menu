<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class ParameterValuesFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('parameter', 'Parameter');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('values', 'Symfony\Component\Form\Extension\Core\Type\CollectionType',  array('label' => 'Значения',
            'type' => new ParameterValueFormType(),
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true));
    }
} 