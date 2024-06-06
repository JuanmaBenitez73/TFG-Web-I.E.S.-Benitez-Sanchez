<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LibraryController extends AbstractController
{
    #[Route('/Biblioteca', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig');
    }
}