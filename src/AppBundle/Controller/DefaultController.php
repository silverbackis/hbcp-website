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

    private function setSeo(string $title = null, string $description)
    {
        $seoPage = $this->container->get(SeoPageInterface::class);
        $seoPage
            ->setTitle(implode(' - ', array_filter([$title, $seoPage->getTitle()])))
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
            return $this->redirectToRoute('news_post', [
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

        return $this->render('frontend/contactus.html.twig', array(
            'title'=>'Contact Us',
            'header_text' => $description
        ));
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(Request $request)
    {
        $description = $this->get(TranslatorInterface::class)->trans('about.description');
        $this->setSeo('About', $description);
        return $this->render('frontend/about.html.twig', [
            'header_text' => $description
        ]);
    }

    /**
     *@Route("/project-team", name="projecteam")
     */
    public function teamMembers()
    {
        $description = $this->get(TranslatorInterface::class)->trans('project_team.description');
        $this->setSeo('Project Team', $description);

        return $this->render('frontend/team.html.twig', array(
            'header_text' =>$description
        ));
    }

    /**
     * @Route("/grant-holders", name="grantholders")
     */
    public function grantholders(Request $request)
    {
        $description = $this->get(TranslatorInterface::class)->trans('grant_holders.description');
        $this->setSeo('Grant Holders', $description);
        return $this->render('frontend/grantholders.html.twig');
    }

    /**
     * @Route("/consultants-collaborators", name="consultants_collaborators")
     */
    public function consultantsCollaborators(Request $request)
    {
        $description = $this->get(TranslatorInterface::class)->trans('consultants_collaborators.description');
        $this->setSeo('Consultants and Collaborators', $description);
        return $this->render('frontend/consultants_collaborators.html.twig');
    }

    /**
     * @param string|null $slug
     * @param Category $parent
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @Route("/resources/{slug}/{parent}", name="resources")
     */
    public function allResources(string $slug = null, Category $parent, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Validate the slug
        $slugify = $this->container->get('slugify');
        $expectedSlug = $slugify->slugify($parent->getName());

        // Ensure we don't have duplicate pages indexed on Google
        if ($expectedSlug !== $slug) {
            return $this->redirectToRoute('resources', [
                'slug' => $expectedSlug,
                'parent' => $parent->getId()
            ]);
        }

        // Checks completed - determine categories to display
        $categoryUtils = $this->container->get(CategoryUtils::class);
        $allCats = $categoryUtils->findAllCategories($parent);
        $allCats[] = $em->getRepository(Category::class)->findOneByName('project');

        if ($request->get('category')) {
            $resetAllCats = false;
            $mergeCats = [];
            foreach ($allCats as $innerCat) {
                if (\in_array($innerCat->getId(), $request->get('category'), false)) {
                    if (!$resetAllCats) {
                        $resetAllCats = true;
                        $allCats = [];
                    }
                    if ($innerCat->getParent()) {
                        $mergeCats[] = $categoryUtils->findAllCategories($innerCat);
                    } else {
                        $allCats[] = $innerCat;
                    }
                }
            }
            $allCats = array_merge($allCats, ...$mergeCats);
        }
        // Get the resources - this function will apply resource type filter and order by as well
        $resources = $em->getRepository(Resource::class)->findByCategories($allCats, $request);

        // If we request just the list (ajax filter)
        if ($request->request->get('req') === 'list') {
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
     * @param string $slug
     * @param Resource|null $resource
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/resource/{slug}/{resource}", name="resource")
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
     * @Route("/privacy-policy", name="privacy")
     */
    public function privacy(Request $request)
    {
        $description = $this->get(TranslatorInterface::class)->trans('privacy.description');
        $this->setSeo('Privacy Policy', $description);
        return $this->render('frontend/privacy.html.twig');
    }
}
