<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('profile', 'User');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userfio', 'text', array('label' => 'ФИО пользователя'))
            ->add('username', 'text', array('label' => 'Логин пользователя'))
            ->add('password', 'repeated', array('required' => false,
                'type' => 'password',
                'invalid_message' => 'Пароли не совпадают',
                'first_options' => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повтор пароля')
            ))
            ->add('email', 'email', array('label' => 'Электронная почта','required' => true))
            ->add('phone', 'text', array('label' => 'Телефон','required' => false))
            ->add('agree', 'checkbox', array('label'=>'Ставя здесь отметку, я даю свое согласие на обработку моих персональных данных в соответствии с законом №152 - ФЗ "О персональных данных" от 27.07.2006 г.', 'required'=>true, 'mapped'=>false));
    }
} 