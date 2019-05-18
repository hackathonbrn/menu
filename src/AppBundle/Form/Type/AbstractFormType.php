<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractFormType extends AbstractType
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    protected $method;
    protected $name;

    public function __construct($name, $method = self::METHOD_POST)
    {
        $this->name = $name;
        $this->method = $method;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('method' => $this->method));
    }
}