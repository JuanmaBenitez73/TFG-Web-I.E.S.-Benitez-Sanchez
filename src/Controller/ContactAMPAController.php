<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactAMPAController extends AbstractController
{
    #[Route('/Contacto_AMPA', name: 'app_contact_AMPA')]
    public function index(): Response
    {
        return $this->render('contact_ampa/index.html.twig');
    }
}