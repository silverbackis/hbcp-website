<?php
namespace AppBundle\Twig;


class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('contentdecode', array($this, 'contentFilter')),
            );
          
    }

    public function contentFilter($webdata)
    {
         $content = base64_decode($webdata);
        $content   =   html_entity_decode($content);
        return $content;
    }


 
}