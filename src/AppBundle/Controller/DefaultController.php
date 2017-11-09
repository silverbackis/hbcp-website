<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Entity\Resource;
use AppBundle\Utils\CategoryUtils;
use AppBundle\Utils\FilterUtils;
use Cocur\Slugify\SlugifyInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            'slugify' => '?'.SlugifyInterface::class,
            CategoryUtils::class => '?'.CategoryUtils::class,
            FilterUtils::class => '?'.FilterUtils::class,
            SeoPageInterface::class => SeoPageInterface::class,
            TranslatorInterface::class => TranslatorInterface::class
        ]);
    }

    private function setSeo(string $title = null, string $description) {
        $seoPage = $this->container->get(SeoPageInterface::class);
        $seoPage
            ->setTitle(join(' - ', array_filter([$title, $seoPage->getTitle()])))
            ->addMeta('name', 'description', $description);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home(Request $request)
    {
        $description = $this->get(TranslatorInterface::class)->trans('home.description');
        $this->setSeo('', $description);
        return $this->render('frontend/homepage.html.twig', [
            'header_text' => $description
        ]);
    }

    /**
     * @Route("/latest-news", name="news")
     */
    public function latestNews(Request $request)
    {
        $description = $this->get(TranslatorInterface::class)->trans('news.description');
        $this->setSeo('Latest News', $description);

        $news = $this->getDoctrine()
            ->getRepository(News::class)
            ->findBy(
                [],
                [
                    'created' => 'DESC'
                ]
            );
        return $this->render('frontend/news.html.twig', [
            'allnews' => $news,
            'header_text' => $description
        ]);
    }

    /**
     * @Route("/news/{slug}/{news}", name="news_post")
     */
    public function showNews($slug, News $news)
    {
        $expectedSlug = $this->container->get('slugify')->slugify($news->getName());
        if ($expectedSlug !== $slug) {
            return $this->redirectToRoute('resources', [
                'slug' => $expectedSlug,
                'news' => $news->getId()
            ]);
        }

        $this->setSeo($news->getName(), $news->getName() . ' - ' . substr(strip_tags($news->getNewsContent()), 0, 250));

        return $this->render('frontend/news-post.html.twig', [
            'news' => $news
        ]);
    }

    /**
     *@Route("/contact-us", name="contactus")
     */
    public function contactUs()
    {
        $description = $this->get(TranslatorInterface::class)->trans('contact.description');
        $this->setSeo('Contact Us', $description);

        return $this->render('frontend/contactus.html.twig',array(
            'title'=>'Contact Us',
            'header_text' => $description
        ));
    }

    /**
     *@Route("/project-team", name="projecteam")
     */
    public function teamMembers()
    {
        $description = $this->get(TranslatorInterface::class)->trans('project_team.description');
        $this->setSeo('Project Team & Grant Holders', $description);

        return $this->render('frontend/team.html.twig',array(
            'header_text' =>$description
        ));
    }

    /**
     *@Route("/resources/{slug}/{parent}", name="resources", defaults={"slug": "", "parent": 0})
     */
    public function allResources(string $slug = null, Category $parent = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $loadDefault = !$parent;
        if ($loadDefault) {
            /**
             * @var null|Category $parent
             */
            $parent = $em->getRepository(Category::class)->findOneByName('project');
            if (!$parent) {
                throw new NotFoundHttpException('Default category not found');
            }
        }

        // Validate the slug
        $slugify = $this->container->get('slugify');
        $expectedSlug = $slugify->slugify($parent->getName());

        if (!$loadDefault) {
            // Project should be loaded as the default resources path
            if ($expectedSlug === 'project') {
                return $this->redirectToRoute('resources');
            }
            // Ensure we don't have duplicate pages indexed on Google
            if ($expectedSlug !== $slug) {
                return $this->redirectToRoute('resources', [
                    'slug' => $expectedSlug,
                    'parent' => $parent->getId()
                ]);
            }
        }

        // Checks completed - determine categories to display
        $categoryUtils = $this->container->get(CategoryUtils::class);
        $allCats = $categoryUtils->findAllCategories($parent);
        if ($request->get('category')) {
            foreach ($allCats as $innerCat) {
                if ($request->request->get('category') == $innerCat->getId())
                {
                    $allCats = $categoryUtils->findAllCategories($innerCat);
                    break;
                }
            }
        }
        // Get the resources - this function will apply resource type filter and order by as well
        $resources = $em->getRepository(Resource::class)->findByCategories($allCats, $request);

        // If we request just the list (ajax filter)
        if ($request->request->get('req') === 'list')
        {
            return new JsonResponse([
               'html' => $this->container->get('twig')->render('frontend/_resourceList.html.twig', ['resources' => $resources])
            ]);
        }

        // Render full page
        $filterUtils = $this->container->get(FilterUtils::class);
        $filterCategories = $filterUtils->getCategoryFilterOptions($resources);
        $filterTypes = $filterUtils->getResourceTypeFilterOptions($resources);

        $title = ucwords($parent->getName()) . ' Resources';
        $hero = $categoryUtils->getCategoryHero($parent);
        $this->setSeo($title, $hero['text']);


        return $this->render('frontend/resources.html.twig', array(
            'title'=> $title,
            'resources' => $resources,
            'hero' => $hero,
            'slug' => $expectedSlug,
            'filter' => [
                'categories' => $filterCategories,
                'types' => $filterTypes
            ]
        ));
    }

    /**
     *@Route("/resource/{slug}/{resource}", name="resource")
     */
    public function viewResource(string $slug, Resource $resource = null)
    {
        if (!$resource) {
            return $this->redirectToRoute('resources');
        }

        $slugify = $this->container->get('slugify');
        $expectedSlug = $slugify->slugify($resource->getTitle());
        if ($expectedSlug !== $slug) {
            return $this->redirectToRoute('resource', [
                'slug' => $expectedSlug,
                'parent' => $resource->getId()
            ]);
        }

        $this->setSeo($resource->getTitle(), $resource->getTitle());

        return $this->render('frontend/resource.html.twig', [
            'title' => $resource->getTitle(),
            'resource' => $resource
        ]);
    }

    /**
     *@Route("/behavioural-science/explain", name="behavioural_science_explain")
     */
    public function behaviouralScienceExplain()
    {
        $description = $this->get(TranslatorInterface::class)->trans('behavioural_science.description');
        $this->setSeo('Explain Behavioural Science Diagram', $description);

        return $this->render('frontend/behaviourDiagramExplain.html.twig', [
            'header_text' => $description
        ]);
    }

    /**
     *@Route("/behavioural-science/{slug}/{parent}", name="behavioural_science", defaults={"slug"="", "id"=0}, requirements={"parent"="\d+"})
     */
    public function behaviouralScience(string $slug = null, Category $parent = null, Request $request)
    {
        $resources = null;
        $tabs = null;
        $secondLevelCategory = null;
        if ($parent) {
            $em = $this->getDoctrine()->getManager();
            $categoryRepo = $em->getRepository(Category::class);
            $bcParent = $categoryRepo->findOneByName('behavioural science');
            $categoryUtils = $this->container->get(CategoryUtils::class);
            $allCats = $categoryUtils->findAllCategories($bcParent);

            if ($parent->getFixed() && in_array($parent, $allCats)) {
                // Validate the slug
                $slugify = $this->container->get('slugify');
                $expectedSlug = $slugify->slugify($parent->getName());
                if ($expectedSlug !== $slug) {
                    return $this->redirectToRoute('behavioural_science', [
                        'slug' => $expectedSlug,
                        'parent' => $parent->getId()
                    ]);
                }
                $isSecondLevel = function (Category $category) {
                    return $category->getParent() && $category->getParent()->getParent();
                };
                $secondLevelCategory = $parent;
                while($isSecondLevel($secondLevelCategory)) {
                    $secondLevelCategory = $secondLevelCategory->getParent();
                }
                $children = $secondLevelCategory->getChildren();
                $firstChild = $children->first();
                $hasTabs = $firstChild && $firstChild->getFixed();
                $tabs = $hasTabs ? $children : null;
                if ($hasTabs) {
                    $isTab = in_array($parent, $children->toArray());
                    if (!$isTab) {
                        $parent = $children->first();
                    }
                }
            } else {
                return $this->redirectToRoute('behavioural_science');
            }
        }

        $description = $this->get(TranslatorInterface::class)->trans('behavioural_science.description');
        $titleArray = [
            $secondLevelCategory ? $secondLevelCategory->getName() : null,
            $parent && $secondLevelCategory!==$parent ? $parent->getName() : null,
            'Behavioural Science Diagram'
        ];
        $this->setSeo(join(' - ', array_filter($titleArray)), $description);

        return $this->render('frontend/behaviour.html.twig', [
            'parent' => $parent,
            'tabs' => $tabs,
            'second_level_link' => $this->generateUrl('behavioural_science', [
                'parent' => $secondLevelCategory ? $secondLevelCategory->getId() : null,
                'slug' => $secondLevelCategory ? $this->get('slugify')->slugify($secondLevelCategory->getName()) : null
            ]),
            'header_text' => $description
        ]);
    }
}
