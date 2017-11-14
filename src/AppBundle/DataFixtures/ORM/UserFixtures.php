<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Get our userManager, you must implement `ContainerAwareInterface`
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername('hbcp');
        $user->setEmail('info@humanbehaviourchange.org');
        $user->setPlainPassword($this->container->getParameter('hbcp_pass'));
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_ADMIN'));
        $userManager->updateUser($user, true);

        $user = $userManager->createUser();
        $user->setUsername('silverback');
        $user->setEmail('info@silverbackis');
        $user->setPlainPassword($this->container->getParameter('silverback_pass'));
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_ADMIN'));
        $userManager->updateUser($user, true);

        // Create admin user
        //$manager->persist($product);

        //$manager->flush();
    }
}
