<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Resource;
use AppBundle\Form\ResourceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminResourcesController
 * @package AppBundle\Controller
 * @Route("/admin", name="admin_")
 */
class AdminResourcesController extends Controller
{

    /**
     * @Route("/resources", name="resources_list")
     */
    public function allResourcesAction()
    {
        $resources = $this->getDoctrine()
            ->getRepository(Resource::class)
            ->findBy([], ['created' => 'DESC']);

        return $this->render('/admin/resources/allresources.html.twig', array(
            'resources' => $resources
        ));
    }

    /**
     * @Route("/resource/{resource}", name="resource", defaults={"resource":0})
     */
    public function createResourceAction(Resource $resource = null, Request $request)
    {
        $new = $resource === null;
        if (!$resource) {
            $resource = new Resource();
        }
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $resource = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($resource);
            $em->flush();
            return $this->redirectToRoute('admin_resources_list');
        }

        return $this->render('/admin/resources/createresource.html.twig', [
            'new' => $new,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/resource/delete/{resource}", name="resource_delete")
     */
    public function deleteResourceAction(Resource $resource = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($resource);
        $em->flush();
        $this->addFlash('notice', 'Resource Deleted');
        return $this->redirectToRoute('admin_resources_list');
    }
}
