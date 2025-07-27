<?php
namespace Drupal\ctwigfilters\TwigExtension;


class CustomFilters1 extends \Twig_Extension {
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('base64_encode', array($this, 'base64_en')),
            new \Twig_SimpleFilter('base64_decode', array($this, 'base64_dec'))
        );
    }

    public function getName()
    {
        return 'TwigExtention.CustomFilters1';
    }

    public function base64_en($input)
    {
       return base64_encode($input);
    }
 
    public function base64_dec($input)
    {
        return base64_decode($input);
    }
}