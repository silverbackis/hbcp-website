<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Category;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

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

    /**
     * @var TranslatorInterface
     */
    private $translator;
    public function __construct(
        SlugifyInterface $slugify,
        EntityManagerInterface $em,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->slugify = $slugify;
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param Category $category
     * @return Category[]
     */
    public function findAllCategories(Category $category): array
    {
        // get all the categories that a resource can be in
        $categories = [$category];
        $categories = array_merge($categories, $this->getCategoryChildren($category));
        return $categories;
    }

    private function getCategoryChildren(Category $category): array
    {
        $children = [];
        $toMerge = [];
        foreach ($category->getChildren() as $child) {
            $children[] = $child;
            if (\count($child->getChildren())) {
                $toMerge[] = $this->getCategoryChildren($child);
            }
        }
        return array_merge($children, ...$toMerge);
    }

    public function getCategoryLinkByName(string $name, $routeName = 'resources')
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
        switch (strtolower($category->getName())) {
            case "behavioural science":
                $hero = [
                    'icon' => 'bundles/app/images/homegraphic-bc.svg',
                    'icon_alt' => 'Hero icon - Behavioural Science Resources',
                    'header' => 'Behavioural Science',
                    'text' => $this->translator->trans('behavioural_science.description')
                ];
                break;
            case "computer science":
                $hero = [
                    'icon' => 'bundles/app/images/homegraphic-cs.svg',
                    'icon_alt' => 'Hero icon - Computer Science Resources',
                    'header' => 'Computer Science',
                    'text' => $this->translator->trans('computer_science.description')
                ];
                break;
            case "system architecture":
                $hero = [
                    'icon' => 'bundles/app/images/homegraphic-sa.svg',
                    'icon_alt' => 'Hero icon - System Architecture Resources',
                    'header' => 'System Architecture',
                    'text' => $this->translator->trans('system_architecture.description')
                ];
                break;
            default:
                $hero = [
                    'icon' => 'bundles/app/images/mob-home.png',
                    'icon_alt' => 'Hero icon - All HBCP Resources',
                    'header' => 'All Resources',
                    'text' => $this->translator->trans('all_resources.description')
                ];
                break;
        }
        return $hero;
    }
}
