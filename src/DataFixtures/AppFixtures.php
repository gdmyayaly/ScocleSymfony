<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $user= new User();
        $user->setPrenom("El Hadji Yaya");
        $user->setNom("LY");
        $user->setUsername("yalya");
        $user->setRoles(["ROLE_ADMIN"]); 
        $password = $this->encoder->encodePassword($user, 'welcome');
        $user->setPassword($password);
        $user1= new User();
        $user1->setPrenom("Cherif");
        $user1->setNom("LY");
        $user1->setUsername("cherif");
        $user1->setRoles(["ROLE_ADMIN"]); 
        $password1 = $this->encoder->encodePassword($user1, 'welcome');
        $user1->setPassword($password1);
        $user1->setStatut("ACTIF");
        $user2= new User();
        $user2->setPrenom("Amadou");
        $user2->setNom("LY");
        $user2->setUsername("amadou");
        $user2->setRoles(["ROLE_ADMIN"]); 
        $password2 = $this->encoder->encodePassword($user2, 'welcome');
        $user2->setPassword($password2);
        $user2->setStatut("BLOQUER");
        $manager->persist($user);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
