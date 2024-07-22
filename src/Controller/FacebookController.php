<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
    #[Route('/connect/facebook', name: 'connect_facebook_start')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'public_profile', 'email', 'user_birthday', 'user_gender'
            ]);
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectCheckAction()
    {
        // Cette méthode ne sera jamais exécutée,
        // car le flux sera intercepté par le FacebookAuthenticator
    }

    #[Route('/politique-confidentialite', name: 'app_confidentialite')]
    public function confidentialite()
    {
        return $this->render('security/confidentialite.html.twig');
    }

    #[Route('/politique-suppression', name: 'app_suppression')]
    public function droitSuppression()
    {
        return $this->render('security/suppresion.html.twig');
    }
}