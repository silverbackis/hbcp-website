<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use AppBundle\Entity\Resources;
use AppBundle\Entity\ResourceType;
use AppBundle\Entity\Category;

class AdminResourcesController extends Controller
{
   
   /**
     * @Route("/admin/allresources", name="resources_list")
     */
    public function allresourcesAction()
    {
        $allresourcedata = $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
               ->findBy([], ['id' => 'DESC']);
   
              
          $allcats = array();
          $allresourcetype = array();

        if(count($allresourcedata) >0 ){

          foreach($allresourcedata as $resource)
          {
        
           $catsname = $this->showCatnameAction((int)$resource->category );

           array_push($allcats,$catsname);
          }
 

         foreach($allresourcedata as $resouname)
         {
           $typeresource = $this->showResourceTypeAction((int)$resouname->resourceType );

           array_push($allresourcetype,$typeresource);
         }
       
         }

         

        return $this->render('/admin/resources/allresources.html.twig', array(
            'alldatas' => $allresourcedata,'catdata'=>$allcats ,'resourcetype' =>$allresourcetype
        ));
    }

  

     /**
     * @Route("/admin/resource/createnew", name="resource_create")
     */

    public function createresourceAction()
    {
       

         $allresourcetype = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
         
         $resourcecategory = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('deepest'=> '1') );


      
           $resourceunlocked = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('fixed'=> '0'));

          return $this->render('/admin/resources/createresource.html.twig', array(
            'resourcetypes' => $allresourcetype,'resourcecategory'=>$this->topcatlog($resourcecategory),'resourceunlockeds' =>$resourceunlocked
        ));

      }

   
     
    /**
     * @Route("/admin/resource/savenew", name="resource_save")
     */

    public function saveresourceAction(Request $request)
    {
            

           $allresources = new \AppBundle\Entity\Resources();
           $allresources ->setTitle($_REQUEST['resourcename']);
           $allresources ->setPathType($_REQUEST['pathtype']);
           $allresources ->setPath($_REQUEST['dropboxurl']);
           $allresources ->setCategory($_REQUEST['catname']);
          $allresources ->setCategoryid($_REQUEST['topcategoryid']);
           
           if($_REQUEST['pathtype'] == 'dropbox'){
           $allresources ->setResourceType($_REQUEST['resourcetype']);
            }
            else{
              $allresources ->setResourceType(0);

            }

           $allresources ->setAddedDatetime(new \DateTime('now'));
           $allresources ->setModifiedDatetime(new \DateTime('now'));

          if($_REQUEST['pathtype'] == 'dropbox'){
            $dropboxfile = $_REQUEST['dropboxurl'] ;

            $operation = $this->dropboxfilechecker($dropboxfile);
              if(!array_key_exists("error",$operation)) { 
             
            $originalDate = strtok($operation['client_modified'],'T');
              $newDate = date("d-m-Y", strtotime($originalDate));
              $filesize = (int)$operation['size'];
               $filesize= $filesize/(1024*1024);
               $filesize = round($filesize, 2);
                $info    = $newDate.'_'.$filesize;
             $allresources ->setResourceInformation($info);

             $this->get('session')->getFlashBag()->clear();
           }
           else
           {
              $request->getSession()
                ->getFlashBag()
                ->add("urlinvalid", "The DropBox URl is invalid.Please Add the Share Link of File");
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);

          }
         }else{

            $url = $_REQUEST['dropboxurl'] ;

            $operation = $this->simpleurlchecker($url);

            if($operation != 1){

             $request->getSession()
                  ->getFlashBag()
                  ->add('urlinvalid', 'Link is invalid');
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);

            }}
              

            $em = $this->getDoctrine()->getManager();
            $em->persist($allresources);
            $em->flush();
            
         
            return $this->redirectToRoute('resources_list');
    }


    /**
     * @Route("/admin/resources/edit/{id}", name="resources_edit")
     * @Method({"GET"})
     */
         
     public function editresourceAction($id, Request $request)
    {
          
          $resourcedata = $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->find($id);

            $allresourcetype = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
         
         $resourcecategory = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('deepest'=> '1') );




         $resourceunlocked = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('fixed'=> '0'),array(
                'topcategory' => 'ASC'));
         
     
        if (empty($resourcedata)) {
            $this->addFlash('error','No Resource Found');
            
            return $this->redirectToRoute('resources_list');
        }



      return $this->render('/admin/resources/editresources.html.twig', array(
            'resourcedata' => $resourcedata, 'resourcetypes' => $allresourcetype,'resourcecategory'=>$this->topcatlog($resourcecategory),'resourceunlockeds' =>$resourceunlocked ,'id' =>$id
        ));


    }
 
    /**
     * @Route("/admin/resources/edit/{id}", name="resources_save")
     * @Method({"POST"})
     */

  public function saveresourceeditAction($id, Request $request)
    {
          $formsave  = $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->find($id);

         if (empty($formsave)) {
            $this->addFlash('error','No Resource is Found');
            
            return $this->redirectToRoute('resources_list');
           }

           
             if(isset($_REQUEST)){
            $formsave  ->setTitle($_REQUEST['resourcename']);
            $formsave  ->setPathType($_REQUEST['pathtype']);
            $formsave  ->setPath($_REQUEST['dropboxurl']);
            $formsave  ->setCategory($_REQUEST['catname']);
           $formsave ->setCategoryid($_REQUEST['topcategoryid']);
           if($_REQUEST['pathtype'] == 'dropbox'){
             $formsave  ->setResourceType($_REQUEST['resourcetype']);
            }
            else{
               $formsave ->setResourceType(0);

            }
            
            $formsave  ->setModifiedDatetime(new \DateTime('now'));
            
              if($_REQUEST['pathtype'] == 'dropbox'){
            $dropboxfile = $_REQUEST['dropboxurl'] ;

            $operation = $this->dropboxfilechecker($dropboxfile);
              if(!array_key_exists("error",$operation)) { 
             
            $originalDate = strtok($operation['client_modified'],'T');
              $newDate = date("d-m-Y", strtotime($originalDate));
              $filesize = (int)$operation['size'];
               $filesize= $filesize/(1024*1024);
               $filesize = round($filesize, 2);
                $info    = $newDate.'_'.$filesize;
             $formsave ->setResourceInformation($info);

             $this->get('session')->getFlashBag()->clear();
           }
           else
           {
              $request->getSession()
                ->getFlashBag()
                ->add("urlinvalid", "The DropBox URl is invalid.Please Add the Share Link of File");
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);

          }
         }else{
         
            $url = $_REQUEST['dropboxurl'] ;

            $operation = $this->simpleurlchecker($url);

            if($operation != 1){

             $request->getSession()
                  ->getFlashBag()
                  ->add("urlinvalid", "Link is Invalid");
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
             
            }

            $formsave ->setResourceInformation(NULL);

          }
          
          
            $em = $this->getDoctrine()->getManager();
            $em->persist( $formsave );
            $em->flush();
       return $this->redirectToRoute('resources_list');
             }

           return $this->render('/admin/resources/editresources.html.twig'
        );
     }
    
     /**
     * @Route("/admin/resource/delete/{id}", name="resource_delete")
     */
    public function resourcedeleteAction($id)
    {
        $deletecat = $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                 ->find($id);
        

        if (empty($deletecat)) {
            $this->addFlash('error', 'No Resource is found');
            
            return $this->redirectToRoute('cats_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($deletecat);
        $em->flush();
        
        
       
        return $this->redirectToRoute('resources_list');
    }
    
     public function showCatnameAction($categoryId)
      {
        $category = $this->getDoctrine()
                     ->getRepository(Category::class)
                     ->find($categoryId);
         if(!empty($category))
         {            
      $catname = $category->getName();
           return $catname;
          }else{

            return 'cat deleted';
          }

      }

    
    public function showResourceTypeAction($resourcetypeId = null)
      {
        if($resourcetypeId != 0)
        {
        $resourcety = $this->getDoctrine()
                     ->getRepository(ResourceType::class)
                     ->find($resourcetypeId);
          if(!empty($resourcety))
          {           
         $nameresource = $resourcety->getName();
           return $nameresource;
         }
         else{
          return 'resourcetype deleted';
            }
         }
       else{

             return 'n/a';
           }
       
       }

  public function dropboxfilechecker($fileurl){
    $auth_token = 'LkY2rQPjf4AAAAAAAAAAbwN41z16XJliiSa3Fa9nIini_jhbrMzS68wScDvyf5OB';
       $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token, 'Content-Type: application/json'));
     curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/sharing/get_shared_link_metadata");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('url'=> $fileurl )));
      $result = curl_exec($ch);
    $array = json_decode(trim($result), TRUE);
    curl_close($ch);
     return $array;
    }
  

    public function simpleurlchecker($url){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_NOBODY, 1);
      curl_setopt($ch, CURLOPT_FAILONERROR, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(curl_exec($ch)!==FALSE)
          {
               return true;
              }
         else
       {
              return 0;
         }
    }

   public  function getBreadcrumbsonresource($cat) {
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
       
             }
       }


      if ($path != "") {
        $path = substr($path,strlen($div)); 
       }
         
        return $path;
      } 
  
       
        public  function topcatlog($resourcecategory){
             $catid = array();
          $cathierchy = array();
          $error = array();

        /*** checks if deepest category present are not ***/
          if(count($resourcecategory )> 0)  {

           
         foreach($resourcecategory  as $resources)
          {
             array_push($catid ,(int)$resources->id);
     
          } 

         foreach($resourcecategory  as $resources)
           {
               $catvals =  $this->getBreadcrumbsonresource((int)$resources->id);
                array_push($cathierchy,$catvals);
           }

           $formattedcats = array_combine($catid ,$cathierchy);
           return $formattedcats;
          }
          else{

            return  $error;
          } 
        }

}

  
 

