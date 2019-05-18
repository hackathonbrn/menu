<?php
namespace AppBundle\Extension;

use Twig_Extension;
use Twig_Filter_Method;

class PriceExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            'price' => new Twig_Filter_Method($this, 'price'),
        );
    }

    public function price($price)
    {
        $b=$price-floor($price);
        if ($b) {
            $decimals=2;
        }
        else {
            $decimals=0;
        }
        return number_format($price,$decimals,',',' ').' руб.';
        
    }

    public function getName()
    {
        return 'price_extension';
    }
}