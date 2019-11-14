<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Category;
use AppBundle\Entity\Resource;
use AppBundle\Utils\CategoryUtils;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
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
            new TwigFilter('contentdecode', array($this, 'contentFilter')),
            new TwigFilter('array_filter', array($this, 'arrayFilter')),
        );
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('category_path', array($this, 'categoryPath')),
            new TwigFunction('category_resources', array($this, 'getResources'))
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
