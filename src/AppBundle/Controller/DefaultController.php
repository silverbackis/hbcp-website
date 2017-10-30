<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\News;
use AppBundle\Entity\Category;
use AppBundle\Entity\ResourceType;
use AppBundle\Entity\Resources;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('frontend/homepage.html.twig',array('title'=>'Homepage-HBCP'));
    }
      
    /**
     * @Route("/latest-news", name="latestnews")
     */
       public function latestnewsAction(Request $request)
    {
    
        $news = $this->getDoctrine()
                ->getRepository('AppBundle:News')
                ->findAll();


        return $this->render('frontend/latestnews.html.twig',array(
            'allnews' => $news ,'title'=>'Latest News - HBCP',
        ));
    }
     
    /**
     * @Route("/news/{slug}/{id}", name="singlenews_list")
     */


     public function showooooAction($slug,$id)
    {
        // $slug will equal the dynamic part of the URL
        // e.g. at /blog/yay-routing, then $slug='yay-routing'

        $singledata = $this->getDoctrine()
                ->getRepository('AppBundle:News')
                ->find($id);
         $title = $singledata->getName();
       
    return $this->render('frontend/singlenews.html.twig',array(
            'allnews' =>$singledata ,'title'=>$title));

    }


   /**
    *@Route("/contact-us", name="contactus")
    */

    public function contactusAction()
    {
        return $this->render('frontend/contactus.html.twig',array(
        'title'=>'Contact Us - HBCP'));

    }

   /**
    *@Route("/project-team", name="projecteam")
    */

    public function teamembersAction()
    {
        return $this->render('frontend/team.html.twig',array(
        'title'=>'Grant Holders - HBCP'));

    }
    

   /**
    *@Route("/behaviour-diagram", name="behaviourdiagram")
    */

    public function BehaviourDiagramAction()
    {
        return $this->render('frontend/behaviourdiagram.html.twig',array(
        'title'=>'Behaviour Diagram - HBCP'));

    }

    /**
    *@Route("/behavioural-science/diagram", name="behavioursciencediagram")
    */
    
     public function BehaviourAction()
    {

        $resourcetypes = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
        
    /*  outcome cats****/
       $outcomecats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '5','fixed'=>'0') );
       
       $wholeoutcomeresult  = array();


    if(!empty(array_filter($outcomecats))){


           $outcomeresult = array();
           $outcomecatis = array();
        foreach($outcomecats as $outcomecat)
        {
            

            $catid = $outcomecat->getId();
            $catname = $outcomecat->getName();
          $outcomeres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($outcomeresult,$outcomeres);
          array_push( $outcomecatis,$catname);
          
 
         }
      $wholeoutcomeresult = array_combine( $outcomecatis,$outcomeresult);
    }
             
        
    /****  Data for reach category      **/

         $reachcats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '11','fixed'=>'0') );
       
       $wholereachresult  = array();


    if(!empty(array_filter($reachcats))){


           $reachresult = array();
           $reachcatis = array();
        foreach($reachcats as $reachcat)
        {
            

            $catid = $reachcat->getId();
            $catname = $reachcat->getName();
          $reachres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($reachresult,$reachres);
          array_push( $reachcatis,$catname);
          
 
         }
      $wholereachresult = array_combine( $reachcatis,$reachresult);
    }
       
   /*********** Data for Engagement********************/

       $engagecats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '12','fixed'=>'0') );
       
       $wholengageresult  = array();


    if(!empty(array_filter($engagecats))){


           $engageresult = array();
           $engagecatis = array();
        foreach($engagecats as $engagecat)
        {
            

            $catid = $engagecat->getId();
            $catname = $engagecat->getName();
          $engageres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($engageresult,$engageres);
          array_push( $engagecatis,$catname);
          
 
         }
      $wholengageresult = array_combine( $engagecatis,$engageresult);
    }
    
    /*********** Data for Context- Population ********************/

       $populationscats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '13','fixed'=>'0') );
       
       $wholpopulationresult  = array();


    if(!empty(array_filter($populationscats))){


           $populationresult = array();
           $populationcatis = array();
        foreach($populationscats as $popcat)
        {
            

            $catid = $popcat->getId();
            $catname = $popcat->getName();
          $popres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($populationresult,$popres);
          array_push( $populationcatis,$catname);
          
 
         }
      $wholpopulationresult = array_combine( $populationcatis,$populationresult);
    }
         /*********** Data for Context- Setting ********************/

       $settingscats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '14','fixed'=>'0') );
       
       $wholsettingresult  = array();


    if(!empty(array_filter($settingscats))){


           $settingresult = array();
           $settingcatis = array();
        foreach($settingscats as $settingcat)
        {
            

            $catid = $settingcat->getId();
            $catname = $settingcat->getName();
          $settingres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($settingresult,$settingres);
          array_push( $settingcatis,$catname);
          
 
         }
      $wholsettingresult = array_combine( $settingcatis,$settingresult);
    }
      
    /*********** Data for Intervention- Content ********************/

       $contentcats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '15','fixed'=>'0') );
       
       $wholecontentresult  = array();


    if(!empty(array_filter($contentcats))){


           $contentresult = array();
           $contentcatis = array();
        foreach($contentcats as $contentcat)
        {
            

            $catid = $contentcat->getId();
            $catname = $contentcat->getName();
          $contentres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($contentresult,$contentres);
          array_push( $contentcatis,$catname);
          
 
         }
      $wholecontentresult = array_combine( $contentcatis,$contentresult);
    }

    /*********** Data for Intervention-   Delivery ********************/

       $delievrycats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '16','fixed'=>'0') );
       
       $wholedeliveryresult  = array();


    if(!empty(array_filter($delievrycats))){


           $deliveryresult = array();
           $deliverycatis = array();
        foreach($delievrycats as $delievrycat)
        {
            

            $catid = $delievrycat->getId();
            $catname = $delievrycat->getName();
          $deliveryres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push($deliveryresult,$deliveryres);
          array_push( $deliverycatis,$catname);
          
 
         }
      $wholedeliveryresult = array_combine( $deliverycatis,$deliveryresult);
    }
    
    /*********** Data for Mechanisms ********************/

       $mechnismscats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '9','fixed'=>'0') );
       
       $wholemechnismsresult  = array();


    if(!empty(array_filter($mechnismscats))){


           $mechresult = array();
           $mechcatis = array();

        foreach($mechnismscats as $mechcat)
        {
            

            $catid = $mechcat->getId();
            $catname = $mechcat->getName();
          $mechnismsres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push( $mechresult,$mechnismsres);
          array_push( $mechcatis,$catname);
          
 
         }
      $wholemechnismsresult = array_combine( $mechcatis,$mechresult);
    }


     /*********** Data for behaviour ********************/

       $behaviourcats = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '10','fixed'=>'0') );
       
       $wholebehaviourresult  = array();


    if(!empty(array_filter($behaviourcats))){


           $behaveresult = array();
           $behavecatis = array();

        foreach($behaviourcats as $behavecat)
        {
            

            $catid = $behavecat->getId();
            $catname = $behavecat->getName();
          $behaviourres= $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->findBy(array('category'=>$catid ) );

       
        
           array_push( $behaveresult,$behaviourres);
          array_push( $behavecatis,$catname);
          
 
         }
      $wholebehaviourresult = array_combine( $behavecatis,$behaveresult);
    }


        return $this->render('frontend/behaviour.html.twig',array(
            'outcomecats' =>$wholeoutcomeresult,'allresourcetypes'=> $resourcetypes,'reachcats'=>$wholereachresult ,'engagecats'=>$wholengageresult ,'populacats'=>$wholpopulationresult ,'settingcats'=>$wholsettingresult ,'contentcats'=>$wholecontentresult,'deliveries' =>$wholedeliveryresult,'mechnismscats'=>$wholemechnismsresult,'behavescats'=>$wholebehaviourresult ,'title' =>'Behavioural Science -HBCP'));
        }

    
    
    /**
    *@Route("/computer-science", name="computerscience")
    */
    
     public function ComputerScienceAction()
    {
        
        $computerscience = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '3','fixed'=>'0') );
          
     $catsdatas = $this->catswithkeys($computerscience);


       $resourcetypes = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
           $doconnect = $this->getDoctrine()->getManager();
             $QUERY = "SELECT resources.id, resources.added_datetime,resources.category,resources.resource_type,resources.title,resources.path_type,category.name,resources.path from resources INNER JOIN category 
                ON resources.category = category.id and category.topcategory = 3
                ORDER BY resources.added_datetime DESC ";
        
               $statement =$doconnect->getConnection()->prepare($QUERY);
               $statement->execute();

               $computerscienceresource = $statement->fetchall();
            
           
        return $this->render('frontend/computerscience.html.twig',array('allcomputerscats'=>$catsdatas ,'allresourcetypes'=> $resourcetypes ,'csresources'=>$computerscienceresource,'title'=>'Computer Science - HBCP'));

    }


    /**
    *@Route("/behavioural-science", name="behaviouralscience")
    */
    
     public function BehaviouralScienceAction()
    {
        
        $behaviourscience = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('topcategory'=> '2') );
          
     $catsdatas = $this->catswithkeys($behaviourscience);


       $resourcetypes = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
           $doconnect = $this->getDoctrine()->getManager();
             $QUERY = "SELECT resources.id, resources.added_datetime,resources.category,resources.resource_type,resources.title,resources.path_type,category.name,resources.path from resources INNER JOIN category 
                ON resources.category = category.id and category.topcategory = 2
                ORDER BY resources.added_datetime DESC ";
        
               $statement =$doconnect->getConnection()->prepare($QUERY);
               $statement->execute();

               $behaviourscienceresource = $statement->fetchall();
            
           
        return $this->render('frontend/behaviourscience.html.twig',array('allcomputerscats'=>$catsdatas ,'allresourcetypes'=> $resourcetypes ,'csresources'=>$behaviourscienceresource,'title'=>'Behavioural Science - HBCP'));

    }

    /**
    *@Route("/system-architecture", name="systemarchitect")
    */
    
     public function SystemArchitectureAction()
     {
         $systemarchitecture = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '4','fixed'=>'0') );
       
       $catsdatas = $this->catswithkeys($systemarchitecture);

       $resourcetypes = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
           $doconnect = $this->getDoctrine()->getManager();
             $QUERY = "SELECT resources.id, resources.added_datetime,resources.category,resources.resource_type,resources.title,resources.path_type,category.name,resources.path from resources INNER JOIN category 
                ON resources.category = category.id and category.topcategory = 4
                ORDER BY resources.added_datetime DESC ";
        
               $statement =$doconnect->getConnection()->prepare($QUERY);
               $statement->execute();

               $systemarchtecture = $statement->fetchall();
    
                   
        return $this->render('frontend/systemarchitect.html.twig',array('allcomputerscats'=>$catsdatas ,'allresourcetypes'=> $resourcetypes ,'csresources'=>$systemarchtecture,'title'=>'System Architecture - HBCP'));

    }
    
    /**
    *@Route("/all-resources", name="allresources")
    */
    public function AllResourcesAction()
    {

        $projetscience = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findBy(array('parentId'=> '1','fixed'=>'0') );

       $catsdatas = $this->catswithkeys($projetscience);         

       $resourcetypes = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();
           $doconnect = $this->getDoctrine()->getManager();
             $QUERY = "SELECT resources.id, resources.added_datetime,resources.category,resources.resource_type,resources.title,resources.path_type,category.name,resources.path from resources INNER JOIN category 
                ON resources.category = category.id and category.topcategory = 1
                ORDER BY resources.added_datetime DESC ";
        
               $statement =$doconnect->getConnection()->prepare($QUERY);
               $statement->execute();

               $allresource = $statement->fetchall();
            
           
          
        return $this->render('frontend/allresources.html.twig',array('allcomputerscats'=>$catsdatas ,'allresourcetypes'=> $resourcetypes ,'csresources'=>$allresource,'title'=>'All Resources - HBCP'));

       

    }

    /**
     *@Route("/resources/{slug}/{id}", name="singleresource")
     */
    public function SingleResourcesAction($slug,$id)
    {
        $resourcetypes = $this->getDoctrine()
                ->getRepository('AppBundle:ResourceType')
                ->findAll();

       $singleresource = $this->getDoctrine()
                ->getRepository('AppBundle:Resources')
                ->find($id);
         $title =   $singleresource->getTitle();     
        
        $departmentid  =   $singleresource->getCategoryid();
        $catid =  $singleresource->getCategory();
           
         $departmentname = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->find($departmentid );
         $finaldepartname =   $departmentname->getName();
          $Catheirchy =  $this->getBreadcrumbsonresource($catid);   
                         

    return $this->render('frontend/singleresource.html.twig',array(
            'resourcedetail' =>$singleresource ,'allresourcetype'=> $resourcetypes,'department'=>$finaldepartname,'cats'=>$Catheirchy,'title'=>$title));
        

    }


    

    /**
     * @Route("/admin/dashboard" , name="admindashboard")
     */
      
     public function admindashboard()
    {
        
   return $this->render('/admin/dashboard/dashboard.html.twig');
    }

   public  function getBreadcrumbsonresource($cat) {
       $path = "";
        $div = "/";
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

     public  function getBreadcrumbsonresourcefull($cat) {
       $path = "";
        $div = ">>";
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
               if($cat == 1 || $cat == 2 || $cat == 3 || $cat == 4)
                { break;
                }
               } /**end while **/

            if ($path != "") {
                $path = substr($path,strlen($div)); 
                }
                return $path;
      }

   public function catswithkeys(array $fetchedcats)
   {  

    $fullcatdetails = array();
          if(!empty(array_filter($fetchedcats)))
          {
                $breadcrumbs = array();
                $catids = array();
             foreach($fetchedcats as $fetchedcat)
             {
        $fullbreadcrumb = $this->getBreadcrumbsonresourcefull($fetchedcat->getID());
         array_push($breadcrumbs,$fullbreadcrumb);
      
        $catid = $fetchedcat->getID();
        array_push($catids,$catid);
             }
      
         $fullcatdetails = array_combine($catids,$breadcrumbs);
          }

        return $fullcatdetails;
    }


}
