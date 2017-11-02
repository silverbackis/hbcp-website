<?php
namespace AppBundle\Twig;


use AppBundle\Utils\CategoryUtils;

class AppExtension extends \Twig_Extension
{
    private $categoryUtils;

    public function __construct(
        CategoryUtils $categoryUtils
    )
    {
        $this->categoryUtils = $categoryUtils;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('contentdecode', array($this, 'contentFilter')),
            new \Twig_SimpleFilter('array_filter', array($this, 'arrayFilter')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('category_path', array($this, 'categoryPath')),
        );
    }

    public function contentFilter($webdata)
    {
        $content = base64_decode($webdata);
        $content   = html_entity_decode($content);
        return $content;
    }

    public function categoryPath(string $catName)
    {
        return $this->categoryUtils->getCategoryLinkByName($catName);
    }

    public function arrayFilter (array $array)
    {
        return array_filter($array);
    }
}