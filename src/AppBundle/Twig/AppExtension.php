<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Category;
use AppBundle\Entity\Resource;
use AppBundle\Utils\CategoryUtils;
use Doctrine\ORM\EntityManagerInterface;

class AppExtension extends \Twig_Extension
{
    private $categoryUtils;
    private $em;

    public function __construct(
        CategoryUtils $categoryUtils,
        EntityManagerInterface $em
    ) {
        $this->categoryUtils = $categoryUtils;
        $this->em = $em;
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
            new \Twig_SimpleFunction('category_resources', array($this, 'getResources'))
        );
    }

    public function contentFilter($webdata)
    {
        $content = base64_decode($webdata);
        $content   = html_entity_decode($content);
        return $content;
    }

    public function categoryPath(string $catName, $routeName = 'resources')
    {
        return $this->categoryUtils->getCategoryLinkByName($catName, $routeName);
    }

    public function arrayFilter(array $array)
    {
        return array_filter($array);
    }

    public function getResources(Category $category)
    {
        return $this->em->getRepository(Resource::class)->findByCategories([$category]);
    }
}
