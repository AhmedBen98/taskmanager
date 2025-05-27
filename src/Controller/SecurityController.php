<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //  Redirige si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_task_index');
        }

        //  Erreur de login éventuelle
        $error = $authenticationUtils->getLastAuthenticationError();

        //  Dernier email saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepte cette méthode automatiquement
        throw new \LogicException('Logout is handled by Symfony security system.');
    }
}
