<?php

namespace AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractEntityFormType extends AbstractFormType
{
    protected $dataClass;
    protected $name;
    protected $validationGroups;

    public function __construct($name, $entity, $validationGroups = array(), $method = self::METHOD_POST)
    {
        parent::__construct($name, $method);

        $this->dataClass = 'AppBundle\Entity\\' . $entity;
        $this->validationGroups = is_array($validationGroups) ? $validationGroups : array($validationGroups);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'method' => $this->method,
            'validation_groups' => $this->validationGroups
        ));
    }
}