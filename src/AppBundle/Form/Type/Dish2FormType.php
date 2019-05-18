<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;

class Dish2FormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('dish', 'Dish');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('photos', 'Symfony\Component\Form\Extension\Core\Type\CollectionType',
            array('label' => 'Фотографии',
                'type' => new ImageUploadFormType(),
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
            ));
    }
} 