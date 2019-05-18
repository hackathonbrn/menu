<?php
/**
 * Created by PhpStorm.
 * User: Mixa
 * Date: 03.04.2017
 * Time: 14:01
 */

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use AppBundle\Form\Type\AbstractEntityFormType;
use AppBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;



class UserFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('user', 'User');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userfio', 'text', array('label' => 'ФИО пользователя'))
            ->add('username', 'text', array('label' => 'Логин пользователя'))
            ->add('password', 'repeated', array('required' => false,
                'type' => 'password',
                'invalid_message' => 'Пароли не совпадают',
                'first_options' => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повтор пароля')
            ))->add('roles', 'entity', array('label' => 'Роли', 'required' => true,
                'class' => 'AppBundle\Entity\Role',
                'property' => 'caption',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('email', 'email', array('label' => 'Электронная почта','required' => true))
            ->add('phone', 'text', array('label' => 'Телефон','required' => false));
    }

}