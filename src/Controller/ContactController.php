<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/Contacto', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig');
    }
}