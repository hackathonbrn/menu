<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DishFilterFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('filter', null);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('caption', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Название', 'required'=>false))
            ->setMethod('GET');
    }
    public function getName()
    {
        return 'form_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('method' => 'GET'));
    }

} 