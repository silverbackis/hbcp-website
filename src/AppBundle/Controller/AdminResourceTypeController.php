<?php
namespace AppBundle\Controller;

use AppBundle\Form\ResourceTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ResourceType;

/**
 * Class AdminResourceTypeController
 * @package AppBundle\Controller
 * @Route("/admin", name="admin_")
 */
class AdminResourceTypeController extends Controller
{
    /**
     * @Route("/resource-types", name="resource_type_list")
     */
    public function resourceTypeIndexAction()
    {
        $resourceTypes = $this->getDoctrine()
            ->getRepository('AppBundle:ResourceType')
            ->findAll();


        return $this->render('/admin/resourcetype/allresources.html.twig', array(
            'resources_types' => $resourceTypes
        ));
    }

    /**
     * @Route("/resource-type/{resourceType}", name="resource_type_item", defaults={"resourceType":0})
     */
    public function createResourceTypeAction(ResourceType $resourceType = null, Request $request)
    {
        $new = $resourceType === null;
        $form = $this->createForm(ResourceTypeType::class, $resourceType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $resourceType = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($resourceType);
            $em->flush();
            $this->addFlash('notice', 'Resource Type ' . ($new ? 'Added' : 'Updated'));
            return $this->redirectToRoute('admin_resource_type_list');
        }
        return $this->render(':admin/resourcetype:createresourcetype.html.twig',array(
            'form' => $form->createView(),
            'new' => $new
        ));
    }

    /**
     * @Route("/resource-type/delete/{resourceType}", name="resource_type_delete")
     */
    public function deleteResourceTypeAction(ResourceType $resourceType)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($resourceType);
        $em->flush();
        $this->addFlash('notice', 'Resource Type Deleted');
        return $this->redirectToRoute('admin_resource_type_list');
    }
}
