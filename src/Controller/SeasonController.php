<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeasonController extends AbstractController
{
    /**
     * @Route("/season", name="season")
     */
    public function index(): Response
    {
        return $this->render('season/index.html.twig', [
            'controller_name' => 'SeasonController',
        ]);
    }
}
