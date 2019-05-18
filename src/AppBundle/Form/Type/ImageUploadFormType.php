<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\AbstractEntityFormType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageUploadFormType extends AbstractEntityFormType
{
    public function __construct()
    {
        parent::__construct('imageupload', 'ImageUpload');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Заголовок картинки', 'required' => false))
                ->add('image', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array('label' => 'Заголовок картинки', 'required' => false))
                ->add('alt', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label' => 'Alt-тэг', 'required' => false))
                ->add('main', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array('label' => 'Главная', 'required' => false))
                ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', array('label' => 'Картинка/изображение', 'required' => false));
    }
} 