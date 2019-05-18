<?php
namespace AppBundle\Extension;

use Twig_Extension;
use Twig_Filter_Method;

class DayExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            'day' => new Twig_Filter_Method($this, 'day'),
        );
    }

    public function day($daynumber)
    {
       switch ($daynumber) {
           case 1:
               return 'Понедельник';
               break;
           case 2:
               return 'Вторник';
               break;
           case 3:
               return 'Среда';
               break;
           case 4:
               return 'Четверг';
               break;
           case 5:
               return 'Пятница';
               break;
           case 6:
               return 'Суббота';
               break;
           case 7:
               return 'Воскресенье';
               break;
       }
           return $daynumber;
    }

    public function getName()
    {
        return 'day_extension';
    }
}