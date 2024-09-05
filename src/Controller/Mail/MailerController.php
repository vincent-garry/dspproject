<?php

namespace App\Controller\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;


class MailerController extends AbstractController
{
    private TransportInterface $transport;
    private TemplatedEmail $email;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }
    public function setMailContent(array $mailContent)
    {
        // Envoi de l'e-mail de bienvenue
        $this->email = (new TemplatedEmail())
            ->from($mailContent['from'])
            ->to($mailContent['to'])
            ->subject($mailContent['subject'])
            ->htmlTemplate($mailContent['htmlTemplate'])
            ->context($mailContent['context']);
    }

    public function send(): SentMessage
    {
        // doesn't like i want 
        // $mailer->send($email);

        return $this->transport->send($this->email);
    }
}
