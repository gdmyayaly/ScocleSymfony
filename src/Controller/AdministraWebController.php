<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
    /**
     * @Route("/web", name="administra_web")
     */
class AdministraWebController extends AbstractController
{
    /**
     * @Route("/administra/web", name="administra_web")
     */
    // public function index()
    // {
    //     return $this->render('administra_web/index.html.twig', [
    //         'controller_name' => 'AdministraWebController',
    //     ]);
    // }
}
