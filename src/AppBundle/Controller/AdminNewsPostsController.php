<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Filesystem\Filesystem;
use AppBundle\Entity\News;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class AdminNewsPostsController extends Controller
{
    /**
     * @Route("/admin/allnews", name="news_list")
     */
    public function indexAction()
    {
        $todos = $this->getDoctrine()
                ->getRepository('AppBundle:News')
                ->findBy([], ['id' => 'DESC']);
        

        return $this->render('/admin/news/allnews.html.twig', array(
            'todos' => $todos
        ));
    }

   /**
     * @Route("/admin/createnew", name="news_create")
     */

    public function createAction(Request $request)
    {
        $todo = new \AppBundle\Entity\News();
        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
       
 $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => $atrributes))
                ->add('author', TextType::class, array('attr' => $atrributes))
                 ->add('newsContent', CKEditorType::class, array('attr' => array('style' => 'margin-bottom:15px')))
                ->add('imagePath', FileType::class, array('attr' => array('style' => 'margin-bottom:15px','class'=>'dropimage'),'required'=>false))
                ->add('imageCredit', TextType::class, array('attr' => array('style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Create News', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request);

               if($form->isSubmitted() && $form->isValid()) {
            $todo->setName($form['name']->getData());
            $todo->setAuthor($form['author']->getData());
            $todo->setAdded(new \DateTime('now'));
            $todo->setLastModified(new \DateTime('now'));
            $todo->setImageCredit($form['imageCredit']->getData());
            $todo->setNewsContent(str_replace('"','\'',$form['newsContent']->getData()));
            $file = $todo->getImagePath();
            
            if($file != '' && !empty($file))
            {

            $fileName = md5(uniqid()).'.'.$file ->guessExtension();
           
            $file->move(
              $this->getParameter('news_directory'),
                $fileName
            );
            $todo->setImagePath($fileName); 
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            
            $this->addFlash('notice', 'News is created');
            
            return $this->redirectToRoute('news_list');
        }
       
        
        return $this->render('/admin/news/createnews.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/news/edit/{id}", name="news_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:News')
                ->find($id);
            $oldimage =  $todo->getImagePath();
       

        
        if (empty($todo)) {
            $this->addFlash('error','No News Found');
            
            return $this->redirectToRoute('news_list');
        }

        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
        
       $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' => $atrributes))
                ->add('author', TextType::class, array('attr' => $atrributes))
                ->add('newsContent', CKEditorType::class, array('attr' => array('style' => 'margin-bottom:15px')))
                ->add('imagePath', FileType::class, array('data_class' => null , 'attr' => array('style' => 'margin-bottom:15px') ,'required' => false))
                ->add('imageCredit', TextType::class, array('attr' => array('style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Update News', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $todo->setName($form['name']->getData());
            $todo->setAuthor($form['author']->getData());
            $todo->setLastModified(new \DateTime('now'));
            $todo->setImageCredit($form['imageCredit']->getData());
        $todo->setNewsContent(str_replace('"','\'',$form['newsContent']->getData()));
            $file = $todo->getImagePath();

             $img = $form['imagePath']->getData();
           


            if($img)
            {
            $fileName = md5(uniqid()).'.'.$file ->guessExtension();
            $file->move(
              $this->getParameter('news_directory'),
                $fileName
            );
            
            $fs = new Filesystem(); 

            $fs->remove($this->get('kernel')->getRootDir().'/../web/uplo‌​ads/newsimages/'.$oldimage);
            

             $todo->setImagePath($fileName); 
            }
            elseif($oldimage !== null){

              $todo->setImagePath($oldimage); 
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            
            $this->addFlash('notice', 'News is updated');
            
            return $this->redirectToRoute('news_list');
        }
        
        return $this->render('/admin/news/editnews.html.twig', array(
            'form' => $form->createView(),
            'todo' => $todo
        ));
    } 
     

        /**
     * @Route("/admin/news/delete/{id}", name="news_delete")
     */
    public function deleteAction($id)
    {
        $todo = $this->getDoctrine()
                ->getRepository('AppBundle:News')
                ->find($id);
        
        if (empty($todo)) {
            $this->addFlash('error', 'No News is found');
            
            return $this->redirectToRoute('todo_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($todo);
        $em->flush();
        
        $this->addFlash('notice', 'News is  removed');
       
        return $this->redirectToRoute('news_list');
    }




    
    
}

  
 

