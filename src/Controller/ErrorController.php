<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

#[AsController]
class ErrorController extends AbstractController
{
    public function __construct(private Environment $twig) {}

    public function __invoke(): Response
    {
        return $this->render('error/404.html.twig', [], new Response('', 404));
    }

    public function show(Request $request, FlattenException $exception): Response
    {
        if ($exception->getStatusCode() === 404) {
            $html = $this->twig->render('errors/404.html.twig');
            return new Response($html, 404);
        }

        // Otras excepciones (500, etc.)
        return new Response(
            $this->twig->render('errors/404.html.twig', [
                'status_code' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ]),
            $exception->getStatusCode()
        );
    }
}