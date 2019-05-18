<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class Dish3FormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('valueadd', 'Dish');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reciepts', 'Symfony\Component\Form\Extension\Core\Type\CollectionType',  array('label' => 'Ингридиенты',
                        'type' => new RecieptFormType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'delete_empty' => true,
        ));
    }
} 