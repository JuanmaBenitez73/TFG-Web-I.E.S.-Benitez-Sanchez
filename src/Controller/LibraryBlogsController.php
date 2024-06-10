<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LibraryBlogsController extends AbstractController
{
    #[Route('/Blogs_Biblioteca', name: 'app_library_blogs')]
    public function index(): Response
    {
        return $this->render('library_blogs/index.html.twig');
    }
}