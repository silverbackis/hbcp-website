<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Category;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class CategoryUtils
{
    /**
     * @var SlugifyInterface
     */
    private $slugify;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var RouterInterface
     */
    private $router;
    public function __construct(
        SlugifyInterface $slugify,
        EntityManagerInterface $em,
        RouterInterface $router
    )
    {
        $this->slugify = $slugify;
        $this->em = $em;
        $this->router = $router;
    }

    public function findAllCategories(Category $category) {
        // get all the categories that a resource can be in
        $categories = [$category];
        $categories = array_merge($categories, $this->getCategoryChildren($category));
        return $categories;
    }

    private function getCategoryChildren (Category $category)
    {
        $children = [];
        foreach ($category->getChildren() as $child) {
            $children[] = $child;
            if (count($child->getChildren())) {
                $children = array_merge($children, $this->getCategoryChildren($child));
            }
        }
        return $children;
    }

    public function getCategoryLinkByName (string $name, $routeName = 'resources')
    {
        $category = $this->em->getRepository(Category::class)->findOneByName($name);
        if (!$category) {
            return null;
        }
        return $this->router->generate($routeName, [
            'parent' =>  $category->getId(),
            'slug' => $this->slugify->slugify($category->getName())
        ]);
    }

    public function getCategoryHero(Category $category)
    {
        switch(strtolower($category->getName())) {
            case "behavioural science":
                $hero = [
                    'icon' => 'bundles/app/images/homegraphic-bc.png',
                    'icon_alt' => 'Hero icon - Behavioural Science Resources',
                    'header' => 'Behavioural Science',
                    'text' => 'The Human Behaviour Change Project is a collaboration between world leading institution to create and develop a Machine Learning Programme that can analyse and literature and no more text'
                ];
                break;
            case "computer science":
                $hero = [
                    'icon' => 'bundles/app/images/homegraphic-cs.svg',
                    'icon_alt' => 'Hero icon - Computer Science Resources',
                    'header' => 'Computer Science',
                    'text' => 'The Human Behaviour Change Project is a collaboration between world leading institution to create and develop a Machine Learning Programme that can analyse and literature and no more text'
                ];
                break;
            case "system architecture":
                $hero = [
                    'icon' => 'bundles/app/images/homegraphic-sa.svg',
                    'icon_alt' => 'Hero icon - System Architecture Resources',
                    'header' => 'System Architecture',
                    'text' => 'The Human Behaviour Change Project is a collaboration between world leading institution to create and develop a Machine Learning Programme that can analyse and literature and no more text'
                ];
                break;
            default:
                $hero = [
                    'icon' => 'bundles/app/images/mob-home.png',
                    'icon_alt' => 'Hero icon - All HBCP Resources',
                    'header' => 'All Resources',
                    'text' => 'The Human Behaviour Change Project is a collaboration between world leading institution to create and
                        develop a Machine Learning Programme that can analyse and literature and no more text'
                ];
                break;
        }
        return $hero;
    }
}
