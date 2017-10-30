<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\ResourceType;


class AdminResourceTypeController extends Controller
{
    /**
     * @Route("/admin/resourcetype/all", name="resourcetype_list")
     */
    public function resourcetypeindexAction()
    {
        $allresources = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
        

        return $this->render('/admin/resourcetype/allresources.html.twig', array(
            'resourcestypes' => $allresources
        ));
    }

   /**
     * @Route("/admin/resourcetype/create", name="resourcetype_create")
     */

    public function resourcetypecreateAction(Request $request)
    {
        $resoucedata = new \AppBundle\Entity\ResourceType();
        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
       
 $form = $this->createFormBuilder($resoucedata)
                ->add('name', TextType::class, array('attr' => $atrributes))
                 ->add('save', SubmitType::class, array('label' => 'Create ResourceType', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()) {
           $resoucedata->setName($form['name']->getData());
           $resoucedata->setCreatedtime(new \DateTime('now'));
           $resoucedata->setUpdatedtime(new \DateTime('now'));
           
            $em = $this->getDoctrine()->getManager();
            $em->persist($resoucedata);
            $em->flush();
            
            $this->addFlash('notice', 'Resource Type is created');
            
            return $this->redirectToRoute('resourcetype_list');
        }
       
        
        return $this->render('/admin/resourcetype/createresourcetype.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/resourcetype/edit/{id}", name="resourcetype_edit")
     */
    public function editresourcetypeAction($id, Request $request)
    {
        $editresourcetype = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->find($id);
           
       

        
        if (empty($editresourcetype)) {
            $this->addFlash('error','No resourcetype is found with this ID');
            
            return $this->redirectToRoute('resourcetype_list');
        }

        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
        
       $form = $this->createFormBuilder($editresourcetype)
                ->add('name', TextType::class, array('attr' => $atrributes))
                ->add('save', SubmitType::class, array('label' => 'Update Resource Type', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $editresourcetype->setName($form['name']->getData());
            $editresourcetype->setUpdatedtime(new \DateTime('now'));
            
           

            $em = $this->getDoctrine()->getManager();
            $em->persist($editresourcetype);
            $em->flush();
            
            $this->addFlash('notice', 'Resource Type is updated');
            
            return $this->redirectToRoute('resourcetype_list');
        }
        
        return $this->render('/admin/resourcetype/editresource.html.twig', array(
            'form' => $form->createView(),
            'resourcedata' => $editresourcetype
        ));
    } 
     

    /**
     * @Route("/admin/resourcetype/delete/{id}", name="resourcetype_delete")
     */
    public function resourcetypedeleteAction($id)
    {
        $deleteresourcetype = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->find($id);
        
        if (empty($deleteresourcetype)) {
            $this->addFlash('error', 'No Resource Type is found');
            
            return $this->redirectToRoute('resourcetype_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($deleteresourcetype);
        $em->flush();
        
        $this->addFlash('notice', 'ResourceType is deleted');
       
        return $this->redirectToRoute('resourcetype_list');
    }




    
    
}

  
 

