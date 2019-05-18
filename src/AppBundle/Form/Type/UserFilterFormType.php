<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFilterFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('filter', null);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('userfio', 'text', array('label' => 'ФИО пользователя', 'required'=>false))
            ->add('username', 'text', array('label' => 'Логин пользователя', 'required'=>false))
            ->add('role', 'entity', array('class'=>'AppBundle\Entity\Role', 'required' => false, 'property'=>'caption' ,'label' => 'Роль пользователя'))
            ->add('email', 'text', array('label' => 'Почта', 'required' => false))
            ->add('phone', 'text', array('label' => 'Телефон', 'required' => false))->setMethod('GET');
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