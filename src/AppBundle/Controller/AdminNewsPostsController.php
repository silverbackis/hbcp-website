<?php

namespace AppBundle\Controller;

use AppBundle\Form\NewsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\News;

/**
 * Class AdminNewsPostsController
 * @package AppBundle\Controller
 * @Route("/admin", name="admin_")
 */
class AdminNewsPostsController extends Controller
{
    /**
     * @Route("/news", name="news_list")
     */
    public function indexAction()
    {
        $newsPosts = $this->getDoctrine()
            ->getRepository('AppBundle:News')
            ->findBy([], ['id' => 'DESC']);


        return $this->render('/admin/news/allnews.html.twig', array(
            'news_posts' => $newsPosts
        ));
    }

    /**
     * @Route("/news-item/{news}", name="news_item", defaults={"news":0})
     */
    public function newsPostAction(News $news = null, Request $request)
    {
        $new = $news === null;
        if (!$news) {
            $news = new News();
        }
        $oldImage = $news->getImage();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var News $news
             */
            $news = $form->getData();

            if ($news->getImage()) {
                if ($oldImage) {
                    unlink($oldImage->getRealPath());
                }
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $news->getImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('news_directory'),
                    $fileName
                );
                $news->setImage(new File($this->getParameter('news_directory') . '/' . $fileName));
            } else {
                $news->setImage($oldImage);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();
            $this->addFlash('notice', 'News Post ' . ($new ? 'added' : 'updated'));
            return $this->redirectToRoute('admin_news_list');
        }

        return $this->render('/admin/news/createnews.html.twig', array(
            'form' => $form->createView(),
            'new' => $new
        ));
    }

    /**
     * @Route("/news-item/delete/{news}", name="news_delete")
     */
    public function newsDeleteAction(News $news)
    {
        $em = $this->getDoctrine()->getManager();
        if ($news->getImage()) {
            unlink($news->getImage()->getRealPath());
        }
        $em->remove($news);
        $em->flush();
        $this->addFlash('notice', 'News Post Deleted');
        return $this->redirectToRoute('admin_news_list');
    }
}
