<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MailerControllerTest extends AbstractController
{
    private TransportInterface $transport;
    private $prizes = [
        ['name' => 'Infuseur à thé', 'image' => 'Infuseur à thé.jpg', 'description' => 'Un infuseur à thé pratique', 'price' => "15,40", 'rank' => 1],
        ['name' => 'Boîte de thé détox', 'image' => 'Boîte de thé détox.jpg', 'description' => '100g de thé détox', 'price' => "25,45", 'rank' => 2],
        ['name' => 'Boîte de thé signature', 'image' => 'Boîte de thé signature.jpg', 'description' => '100g de thé signature', 'price' => "78,99", 'rank' => 3],
        ['name' => 'Coffret découverte 39 €', 'image' => 'Coffret découverte 39€.jpg', 'description' => 'Un assortiment de thés', 'price' => "39", 'rank' => 0],
        ['name' => 'Coffret découverte 69 €', 'image' => 'Coffret découverte 69€.jpg', 'description' => 'Notre meilleur assortiment', 'price' => "69", 'rank' => 0]
    ];

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }
    #[Route('/sendCodeValidationWin')]
    public function sendCodeValidationWin(MailerInterface $mailer): Response
    {

        $context = [
            'prize' => $this->prizes[0],
            'name' => 'Patosh',
            'mail' => 'cpatop@outlook.fr',
            'claim_url' =>
            $this->generateUrl('app_my_prize', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $htmlTemplate = 'email/templates/code_validation_win.html.twig';
        // Envoi de l'e-mail de bienvenue
        $email = (new TemplatedEmail())
            ->from('noreply@thetiptop.com')
            ->to('cpatop@outlook.fr')
            ->subject('Bienvenue chez Thé Tip Top !')
            ->htmlTemplate($htmlTemplate)
            ->context($context);
        $mailer->send($email);

        // doesn't like i want 
        // $mailer->send($email);

        $response = $this->transport->send($email);

        return $this->render(
            $htmlTemplate,
            $context
        );
    }

    #[Route('/sendConfirmationEmail')]
    public function sendConfirmationEmail(MailerInterface $mailer): Response
    {


        $context = [
            'name' => "XXX",
            'mail' => "XXX",
            'confirmation_url' =>
            $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'expiresAtMessageKey' =>  "XXX",
            'expiresAtMessageData' => "XXX"
        ];
        $htmlTemplate = 'email/templates/confirmation_email.html.twig';

        // Envoi de l'e-mail de bienvenue
        $email = (new TemplatedEmail())
            ->from('noreply@thetiptop.com')
            ->to('cpatop@outlook.fr')
            ->subject('Bienvenue chez Thé Tip Top !')
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        // doesn't work like i want 
        // $mailer->send($email);

        // so to send :
        $response = $this->transport->send($email);

        return $this->render(
            $htmlTemplate,
            $context
        );
    }


    #[Route('/sendDeleteAccountEmail')]
    public function sendDeleteAccountEmail(MailerInterface $mailer): Response
    {
        $context = [
            'name' => "XXX",
            'mail' => "XXX",
            'cancel_deletion_url' =>
            $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL)

        ];
        $htmlTemplate = 'email/templates/confirmation_delete_account.html.twig';
        // Envoi de l'e-mail de bienvenue
        $email = (new TemplatedEmail())
            ->from('noreply@thetiptop.com')
            ->to('cpatop@outlook.fr')
            ->subject('Bienvenue chez Thé Tip Top !')
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        // doesn't like i want 
        // $mailer->send($email);

        // so to send :
        $response = $this->transport->send($email);

        return $this->render(
            $htmlTemplate,
            $context
        );
    }

    #[Route('/sendBigWinnerEmail')]
    public function sendBigWinnerEmail(MailerInterface $mailer): Response
    {
        $context = [
            "grand_prize" => $this->prizes[0],
            'name' => "XXX",
            'mail' => "XXX",
            'claim_url' =>
            $this->generateUrl('app_my_prizes', [], UrlGeneratorInterface::ABSOLUTE_URL)

        ];

        $htmlTemplate = 'email/templates/big_winner.html.twig';
        // Envoi de l'e-mail de bienvenue
        $email = (new TemplatedEmail())
            ->from('noreply@thetiptop.com')
            ->to('cpatop@outlook.fr')
            ->subject('Bienvenue chez Thé Tip Top !')
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        // doesn't like i want 
        // $mailer->send($email);

        // so to send :
        $response = $this->transport->send($email);

        return $this->render(
            $htmlTemplate,
            $context
        );
    }


    // #[Route('/email')]
    // public function sendTestEmail(): JsonResponse
    // {
    //     $message = (new MailtrapEmail())
    //         ->from('from@xample.com')
    //         ->to('cpatop@outlook.fr')
    //         ->cc('thesuperdave404@gmail.com')
    //         ->priority(Email::PRIORITY_HIGH)
    //         ->subject('Test email')
    //         ->text('text')
    //         ->category('category')
    //         ->customVariables([
    //             'var1' => 'value1',
    //             'var2' => 'value2'
    //         ]);

    //     $response = $this->transport->send($message);

    //     return new JsonResponse(['messageId' => $response->getMessageId()]);
    // }
    // #[Route('/email')]
    // public function test(MailerInterface $mailer): void
    // {
    // $email = (new Email())
    //     ->from('hello@example.com')
    //     ->to('you@example.com')
    //     ->cc('cpatop@outlook.fr')

    //     ->subject('Time for Symfony Mailer!')
    //     ->text('Sending emails is fun again!')
    //     ->html('<p>See Twig integration for better HTML integration!</p>');

    // $mailer->send($email);

    // $prizes = [
    //     ['name' => 'Infuseur à thé', 'image' => 'Infuseur à thé.jpg', 'description' => 'Un infuseur à thé pratique', 'price' => "15,40", 'rank' => 1],
    //     ['name' => 'Boîte de thé détox', 'image' => 'Boîte de thé détox.jpg', 'description' => '100g de thé détox', 'price' => "25,45", 'rank' => 2],
    //     ['name' => 'Boîte de thé signature', 'image' => 'Boîte de thé signature.jpg', 'description' => '100g de thé signature', 'price' => "78,99", 'rank' => 3],
    //     ['name' => 'Coffret découverte 39 €', 'image' => 'Coffret découverte 39€.jpg', 'description' => 'Un assortiment de thés', 'price' => "39", 'rank' => 0],
    //     ['name' => 'Coffret découverte 69 €', 'image' => 'Coffret découverte 69€.jpg', 'description' => 'Notre meilleur assortiment', 'price' => "69", 'rank' => 0]
    // ];
    // Envoi de l'e-mail de bienvenue
    // $email = (new TemplatedEmail())
    //     ->from('noreply@thetiptop.com')
    //     ->to('cpatop@outlook.fr')
    //     ->subject('Bienvenue chez Thé Tip Top !')
    //     ->htmlTemplate('email/mail.html.twig')
    //     ->context([
    //         'prizes' => $prizes,
    //         'fullName' => 'Patosh',
    //     ]);


    // $mailer->send($email);
    // $response = $this->transport->send($email);
    // return $this->render(
    //     'registration/confirmation_email.html.twig',
    //     [
    //         'name' => "test",
    //         'signedUrl' => "test",
    //         // 'expiresAtMessageKey' => "test",
    //         // 'expiresAtMessageData' => "test"
    //     ]
    // );
    // return $this->render(
    //     'registration/confirmation_email.html.twig',
    //     [
    //         'name' => "test",
    //         'signedUrl' => "test",
    //         // 'expiresAtMessageKey' => "test",
    //         // 'expiresAtMessageData' => "test"
    //     ]
    // );
    // return new Response("Email was sent : " . $response->getMessageId());
    // }
}
