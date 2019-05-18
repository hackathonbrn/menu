<?php
namespace AppBundle\Extension;

use Twig_Extension;
use Twig_Filter_Method;

class RussianDateExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            'russian_date' => new Twig_Filter_Method($this, 'russianDate'),
        );
    }

    public function russianDate(\DateTime $date = null)
    {
        if ($date) {
            $months = [1 => 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
            //$date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            $key = $date->format('n');
            return $date->format('d ' . $months[$key] . ' Y');
        }
        else
            return null;
    }

    public function getName()
    {
        return 'russian_date_extension';
    }
}