<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Entity\Resource;
use AppBundle\Utils\CategoryUtils;
use AppBundle\Utils\FilterUtils;
use Cocur\Slugify\SlugifyInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends AbstractController
{
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            'slugify' => '?'.SlugifyInterface::class,
            CategoryUtils::class => '?'.CategoryUtils::class,
            FilterUtils::class => '?'.FilterUtils::class
        ]);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home(Request $request)
    {
        return $this->render('frontend/homepage.html.twig', [
            'title'=>'Welcome'
        ]);
    }

    /**
     * @Route("/latest-news", name="latestnews")
     */
    public function latestNews(Request $request)
    {
        $news = $this->getDoctrine()
            ->getRepository(News::class)
            ->findAll();
        return $this->render('news.html.twig', [
            'allnews' => $news,
            'title'=>'Latest News',
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

        return $this->render('frontend/singlenews.html.twig', [
            'news' => $news,
            'title'=> $news->getName()
        ]);
    }

    /**
     *@Route("/contact-us", name="contactus")
     */
    public function contactUs()
    {
        return $this->render('frontend/contactus.html.twig',array(
            'title'=>'Contact Us'));
    }

    /**
     *@Route("/project-team", name="projecteam")
     */
    public function teamMembers()
    {
        return $this->render('frontend/team.html.twig',array(
            'title'=>'Grant Holders'));
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
        return $this->render('frontend/resources.html.twig', array(
            'title'=> ucwords($parent->getName()) . ' Resources',
            'resources' => $resources,
            'hero' => $categoryUtils->getCategoryHero($parent),
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

        return $this->render('frontend/resource.html.twig', [
            'title' => $resource->getTitle(),
            'resource' => $resource
        ]);
    }

    /**
     *@Route("/behavioural-science", name="behavioural_science")
     */
    public function behaviouralScience()
    {
        return $this->render('frontend/behaviour.html.twig', [
            'title' => 'Behavioural Science'
        ]);
    }

    /**
     *@Route("/behavioural-science/explain", name="behavioural_science_explain")
     */
    public function behaviouralScienceExplain()
    {
        return $this->render('frontend/behaviourdiagram.html.twig', [
            'title'=>'Explain Behavioural Science Diagram'
        ]);
    }
}
