<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReplyRepository")
 */
class Reply
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private string $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="replies")
     * @Assert\NotBlank()
     */
    private User $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ticket", inversedBy="replies", cascade={"persist", "remove"})
     * @Serializer\Exclude()
     * @Assert\NotBlank()
     */
    private Ticket $ticket;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }
}
