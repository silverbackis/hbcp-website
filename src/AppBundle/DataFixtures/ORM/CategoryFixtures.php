<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $project = $this->generateCategory('Project');
        $behaviouralScience = $this->generateCategory('Behavioural Science');
        $computerScience = $this->generateCategory('Computer Science');
        $systemArchitecture = $this->generateCategory('System Architecture');

        $outcome = $this->generateCategory('Outcome', $behaviouralScience);
        $exposure = $this->generateCategory('Exposure', $behaviouralScience);
        $context = $this->generateCategory('Context', $behaviouralScience);
        $intervention = $this->generateCategory('Intervention', $behaviouralScience);
        $mechanisms = $this->generateCategory('Mechanisms', $behaviouralScience);
        $behaviour = $this->generateCategory('Behaviour', $behaviouralScience);

        $reach = $this->generateCategory('Reach', $exposure);
        $engagement = $this->generateCategory('Engagement', $exposure);

        $population = $this->generateCategory('Population', $context);
        $setting = $this->generateCategory('Setting', $context);

        $content = $this->generateCategory('Content', $intervention);
        $delivery = $this->generateCategory('Delivery', $intervention);

        $manager->flush();
    }

    private function generateCategory($catName, Category $parent = null)
    {
        $category = new Category();
        $category->setName($catName);
        $category->setFixed(true);
        $category->setParent($parent);
        $this->manager->persist($category);
        return $category;
    }
}
