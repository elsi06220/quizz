<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{

    #[Route('/about', name: 'AboutPage')]
    public function AboutPage(): Response
    {
        return $this->render('about/about.html.twig', [
            'controller_name' => 'AboutController',
        ]);
    }
}
