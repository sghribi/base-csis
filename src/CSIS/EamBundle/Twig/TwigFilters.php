<?php

namespace CSIS\EamBundle\Twig;


class TwigFilters extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('slice', array($this, 'sliceFilter')),
        );
    }

    public function sliceFilter($string)
    {
        if(strlen($string) > 30) {
            $string = substr($string, 0, 30).'…';
        }

        return $string;
    }

    public function getName()
    {
        return 'csis_extension';
    }
}
