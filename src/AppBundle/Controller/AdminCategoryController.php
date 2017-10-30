<?php
namespace AppBundle\Controller;

use AppBundle\Form\CreateCategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Category;

class AdminCategoryController extends Controller
{
    /**
     * @Route("/admin/cats/all", name="cats_list")
     */
    public function categoryIndexAction()
    {
        $allresources = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findBy(array('fixed'=> '0'),array('id' => 'DESC') );

        $errors = array_filter($allresources);

        $topcategory = array();
        $cathierchy = array();
        if(!empty($errors)) {
            foreach($allresources as $resource)
            {
                $gettheparentid = $this->getCatparentID((int)$resource->id);
                $catsname = $this->showTopcatAction((int)$gettheparentid );

                array_push($topcategory,$catsname);
            }
            foreach($allresources as $resourcecats)
            {
                $catheirchy  =  $this->getBreadcrumbsonfront((int)$resourcecats->parentId);
                array_push($cathierchy,$catheirchy);
            }
        }

        return $this->render('/admin/category/allcats.html.twig', array(
            'catdatas' => $allresources ,'topcats'=>$topcategory,'catsheirchy'=>$cathierchy


        ));
    }

    /**
     * @Route("/admin/cat/create", name="category_create")
     */
    public function createCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CreateCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $this->addFlash('notice', 'Category added');
            return $this->redirectToRoute('cats_list');
        }
        return $this->render('/admin/category/createcat.html.twig',array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/cat/edit/{id}", name="cat_edit")
     * @Method({"GET"})
     */
    public function editAction($id, Request $request)
    {


        $resultcategory = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);

        if (empty($resultcategory)) {
            $this->addFlash('error','No Category Found');
            return $this->redirectToRoute('cats_list');
        }

        $topcategory = array();
        $cathierchy = array();

        $resourcecategory = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findBy(array('deepest'=> '1') );

        $errors = array_filter($resourcecategory);


        if(!empty($errors))  {

            $catid = array();
            foreach($resourcecategory  as $resources)
            {
                array_push($catid ,(int)$resources->id);

            }


            $cathierchy = array();

            foreach($resourcecategory  as $resources)
            {
                $catvals =  $this->getBreadcrumbs((int)$resources->id);
                array_push($cathierchy,$catvals);
            }

            $formattedcats = array_combine($catid ,$cathierchy);
        }





        return $this->render('/admin/category/editcat.html.twig', array(

            'catinfos' => $resultcategory,'deepcats'=> $formattedcats
        ));
    }



    /**
     * @Route("/admin/cat/edit/{id}", name="cat_edit_save")
     * @Method({"POST"})
     */

    public function editcategorysaveAction($id, Request $request)
    {
        $formsave  = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);

        $formresources =  $this->getDoctrine()
            ->getRepository('AppBundle:Resources')
            ->findBy(array('category'=> $id ) );

        $errors = array_filter($formresources);

        if (empty($formsave)) {
            $this->addFlash('error','No Resource is Found');

            return $this->redirectToRoute('cats_list');
        }


        if(isset($_REQUEST)){

            $formsave ->setName($_REQUEST['categoryname']);
            $formsave ->setParentId($_REQUEST['parentid']);
            $toparentcat =$this->getCatTopparentID($_REQUEST['parentid']);
            $formsave ->setTopcategory($toparentcat);

            $formsave->setUpdatedTime(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($formsave);
            $em->flush();


            if(!empty($errors))
            {

                foreach($formresources as $formresource)
                {
                    $formresource->setCategoryid($_REQUEST['parentid']);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($formresource);
                    $em->flush();

                }


            }

            $this->addFlash('notice', 'Category is updated');
            return $this->redirectToRoute('cats_list');
        }

        return $this->render('/admin/resources/editresources.html.twig'
        );
    }

    /**
     * @Route("/admin/cat/delete/{id}", name="cat_delete")
     */
    public function categorydeleteAction($id)
    {
        $deletecat = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);
        $catresource = $this->getDoctrine()
            ->getRepository('AppBundle:Resources')
            ->findBy(array('categoryid'=> $id) );

        $errors = array_filter($catresource);


        if (empty($deletecat)) {
            $this->addFlash('error', 'No Category is found');

            return $this->redirectToRoute('cats_list');
        }


        if(!empty($errors))
        {

            foreach($catresource as $catsource)
            {
                $catsource->setCategoryid('0');
                $em = $this->getDoctrine()->getManager();
                $em->persist($catsource);
                $em->flush();

            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($deletecat);
        $em->flush();



        $this->addFlash('notice', 'Category is  removed');

        return $this->redirectToRoute('cats_list');
    }

    public function getBreadcrumbs(Category $category)
    {
        return "Stop stupid breadcrumb creation";
    }


    public  function getBreadcrumbsonfront($cat) {
        $path = "";
        $div = " >> ";
        while ($cat != 0) {
            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = " SELECT * FROM category where  id=" .$cat . " ";
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            $row = $statement->fetchall();
            $row = $row [0];
            if ($row) {
                $path = $div.$row['name'].$path;
                $cat = $row['parentId'];
                if($cat == 1 || $cat == 2 || $cat == 3 || $cat == 4)
                { break;
                }}
        } /**end while **/

        if ($path != "") {
            $path = substr($path,strlen($div));
        }
        return $path;
    }

    public  function getCatparentID($cat) {
        while ($cat != 0) {
            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = " SELECT * FROM category where  id=" .$cat . " ";
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            $row = $statement->fetchall();
            $row = $row [0];
            if ($row) {
                $cat = $row['parentId'];
                if($cat == 1 || $cat == 2 || $cat == 3 || $cat == 4)
                {
                    break;
                }}
        }
        return $cat;
    }


    public  function getCatTopparentID($cat) {
        while ($cat != 0) {
            $em = $this->getDoctrine()->getManager();
            $RAW_QUERY = " SELECT * FROM category where  id=" .$cat . " ";
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            $row = $statement->fetchall();
            $row = $row [0];
            if ($row) {
                $cat = $row['parentId'];

                if($cat == 0 )
                {
                    $cat = $row['id'];
                    break;
                }

            }
        }
        return $cat;
    }

    public function showTopcatAction($categoryId)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($categoryId);
        $catname = $category->getName();
        return $catname;
    }

}

  
 


