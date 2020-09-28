<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
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
        $tab=["Femme","Homme","Enfant","Cosmetique","Accessoires"];
        for ($i=0; $i <count($tab) ; $i++) { 
            $categorie=new Categorie();
            $categorie->setNom($tab[$i]);
            $manager->persist($categorie);
        }
        $manager->flush();
    }
}
