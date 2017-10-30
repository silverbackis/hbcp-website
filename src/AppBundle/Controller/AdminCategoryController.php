<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminCategoryController
 * @package AppBundle\Controller
 * @Route("/admin")
 */
class AdminCategoryController extends Controller
{
    /**
     * @Route("/categories", name="categories")
     */
    public function categoryIndexAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(array('fixed'=> '0'),array('created' => 'DESC') );

        return $this->render('/admin/category/allcats.html.twig', array(
            'categories' => $categories
        ));
    }

    /**
     * @Route("/category/{category}", name="category_edit", defaults={"category":0})
     */
    public function createCategoryAction(Category $category = null, Request $request)
    {
        $newCat = $category === null;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('notice', 'Category ' . ($newCat ? 'added' : 'updated'));
            return $this->redirectToRoute('categories');
        }
        return $this->render('/admin/category/createcat.html.twig',array(
            'form' => $form->createView(),
            'new' => $newCat
        ));
    }

    /**
     * @Route("/category/delete/{category}", name="category_delete")
     */
    public function categoryDeleteAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('notice', 'Category deleted');
        return $this->redirectToRoute('categories');
    }
}
