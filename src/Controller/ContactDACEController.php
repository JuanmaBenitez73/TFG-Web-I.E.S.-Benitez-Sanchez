<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactDACEController extends AbstractController
{
    #[Route('/Contacto_DACE', name: 'app_contact_DACE')]
    public function index(): Response
    {
        return $this->render('contact_dace/index.html.twig');
    }
}