<?php

namespace App\Controller\Mail;

use App\Entity\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;


class MailerController extends AbstractController
{
    private TransportInterface $transport;
    private TemplatedEmail $email;
    private Email $mailRecord;
    private EntityManagerInterface $entityManager;

    public function __construct(TransportInterface $transport, EntityManagerInterface $entityManager)
    {
        $this->transport = $transport;
        $this->entityManager = $entityManager;
    }
    public function setMailContent(array $mailContent)
    {
        $this->email = (new TemplatedEmail())
            ->from($mailContent['from'])
            ->to($mailContent['to'])
            ->subject($mailContent['subject'])
            ->htmlTemplate($mailContent['htmlTemplate'])
            ->context($mailContent['context']);

        $this->mailRecord = new Email();
        $this->mailRecord->setFrom($mailContent['from']->getAddress());
        $this->mailRecord->setTo($mailContent['to']);
        $this->mailRecord->setSubject($mailContent['subject']);
        $this->mailRecord->setHtmlTemplate($mailContent['htmlTemplate']);
        $this->mailRecord->setContext($mailContent['context']);

        $this->entityManager->persist($this->mailRecord);
        $this->entityManager->flush();
    }

    public function send(): SentMessage
    {
        $sentMessage = $this->transport->send($this->email);

        $this->mailRecord->setSend(true);
        $this->mailRecord->setDeliveredAt(new \DateTimeImmutable());

        $this->entityManager->persist($this->mailRecord);
        $this->entityManager->flush();

        return $sentMessage;
    }
}
