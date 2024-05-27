<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EducativeOfferController extends AbstractController
{
    #[Route('/Oferta_Educativa', name: 'app_educative_offer')]
    public function index(): Response
    {
        return $this->render('educative_offer/index.html.twig');
    }
}