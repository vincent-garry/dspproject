<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectGoogle(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'email',
                'profile',
                'https://www.googleapis.com/auth/user.birthday.read',
                'https://www.googleapis.com/auth/user.gender.read',
                'https://www.googleapis.com/auth/user.addresses.read'
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheck(): Response
    {
        // Cette méthode ne sera jamais exécutée.
        // Elle est interceptée par le GoogleAuthenticator
        throw new \LogicException('This method can be blank - it will be intercepted by the Google authenticator');
    }

    // FOOTER
    #[Route('/mentions-legales', name: 'app_mention_legale')]
    public function mentionsLegales(): Response
    {
        return $this->render('security/mentions-legales.html.twig');
    }

    #[Route('/reglement', name: 'app_reglement')]
    public function reglement(): Response
    {
        return $this->render('security/reglement.html.twig');
    }

    #[Route('/politique-confidentialité', name: 'app_confidentialite_application')]
    public function confidentialite(): Response
    {
        return $this->render('security/confidentialite-application.html.twig');
    }

    #[Route('/politique-conservation', name: 'app_conservation')]
    public function conservation(): Response
    {
        return $this->render('security/conservation.html.twig');
    }

    #[Route('/conditions-générales', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('security/cgu.html.twig');
    }
}
