<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: '`from`', length: 255)]
    private ?string $from = null;

    #[ORM\Column(name: '`to`', length: 255)]
    private ?string $to = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $htmlTemplate = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $context = null;

    #[ORM\Column]
    private ?bool $send = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deliveredAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getHtmlTemplate(): ?string
    {
        return $this->htmlTemplate;
    }

    public function setHtmlTemplate(?string $htmlTemplate): self
    {
        $this->htmlTemplate = $htmlTemplate;
        return $this;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function isSend(): ?bool
    {
        return $this->send;
    }

    public function setSend(bool $send): self
    {
        $this->send = $send;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeliveredAt(): ?\DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?\DateTimeImmutable $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;
        return $this;
    }
}
