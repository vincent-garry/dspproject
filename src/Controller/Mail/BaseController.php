<?php

namespace App\Controller\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;


class BaseController extends AbstractController
{
    protected MailerController $MAILER;

    public function __construct(TransportInterface $transport)
    {
        $this->MAILER = new MailerController($transport);
    }
}
