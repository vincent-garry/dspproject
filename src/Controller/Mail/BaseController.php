<?php

namespace App\Controller\Mail;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Transport\TransportInterface;


class BaseController extends AbstractController
{
    protected MailerController $MAILER;

    public function __construct(TransportInterface $transport, EntityManagerInterface $entityManager)
    {
        $this->MAILER = new MailerController($transport, $entityManager);
    }
}
