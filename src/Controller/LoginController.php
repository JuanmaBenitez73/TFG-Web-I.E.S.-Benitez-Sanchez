<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\RequestStack;

class LoginController extends AbstractController
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        $user = $this->em->getRepository(Users::class)->findOneBy(['username' => $username]);

        if (!$user) {
            $this->addFlash('error', 'Usuario no encontrado');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($user->getPassword() === $password) {
            $session->set('user_id', $user->getId());
            $session->set('username', $user->getUsername());
            $session->set('is_admin', (bool) $user->isAdmin());
            $this->addFlash('success', 'Has iniciado sesión correctamente');
        } else {
            $this->addFlash('error', 'Contraseña incorrecta');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request, SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirect('/');
    }
}
