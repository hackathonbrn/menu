<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('profile', 'User');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userfio', 'text', array('label' => 'ФИО пользователя (можно поменять только через запрос администратору)', 'disabled'=>true))
            ->add('username', 'text', array('label' => 'Логин пользователя', 'disabled'=> true))
            ->add('password', 'repeated', array('required' => false,
                'type' => 'password',
                'invalid_message' => 'Пароли не совпадают',
                'first_options' => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повтор пароля')
            ))
            ->add('email', 'email', array('label' => 'Электронная почта','required' => true))
            ->add('phone', 'text', array('label' => 'Телефон','required' => false));
    }
} 