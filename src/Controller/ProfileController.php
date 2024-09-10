<?php

namespace App\Controller;

use App\Controller\Mail\BaseController;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Twig\TwigFunction;

class ProfileController extends BaseController
{

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Mettre à jour l'utilisateur
            $user->setFirstName($data->getFirstName());
            $user->setLastName($data->getLastName());
            $user->setGender($data->getGender());
            $user->setBirthdate($data->getBirthdate());
            $user->setAddress($data->getAddress());

            // Persister les changements
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success_profile_update', 'Votre profil a été mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'isProfilePage' => true,
        ]);
    }

    #[Route('/deleteProfile', name: 'app_profile_delete')]
    public function deleteProfile(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user->setDeleted(true);

        // Persister les changements
        $entityManager->persist($user);
        $entityManager->flush();

        $mailContent = [
            'from' => new Address('noreply@thetiptop.com', 'No Reply'),
            'to' => $user->getEmail(),
            'subject' => 'Votre compte à bien été supprimer',
            'htmlTemplate' => 'email/templates/confirmation_delete_account.html.twig',
            'context' => [
                'name' => $user->getFullName(),
                'mail' => $user->getEmail(),
            ]
        ];

        $this->MAILER->setMailContent($mailContent);

        try {
            $this->MAILER->send();
        } catch (\Exception $e) {
            $this->addFlash('warning_mail_not_send', 'Erreur lors de l\'envoi de l\'email');
            return $this->redirectToRoute('app_logout');
        }

        // Ajouter un message flash
        $this->addFlash('success_profile_delete', 'Votre compte a été supprimer avec succès');
        return $this->redirectToRoute('app_logout');
    }
}
