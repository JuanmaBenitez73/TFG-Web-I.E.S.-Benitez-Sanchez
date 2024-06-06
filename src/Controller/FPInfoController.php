<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FPInfoController extends AbstractController
{
    #[Route('/Información_FP', name: 'app_fp_info')]
    public function index(): Response
    {
        return $this->render('fp_info/index.html.twig');
    }
}