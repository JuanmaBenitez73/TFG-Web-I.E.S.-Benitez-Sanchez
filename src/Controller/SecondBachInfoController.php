<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecondBachInfoController extends AbstractController
{
    #[Route('/Información_2º_bachillerato', name: 'app_second_bach_info')]
    public function index(): Response
    {
        return $this->render('second_bach_info/index.html.twig');
    }
}