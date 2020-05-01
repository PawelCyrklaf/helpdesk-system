<?php

namespace App\Service;

use App\Entity\Ticket;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mime\Email;

class EmailTemplateService
{
    public const NEW_TICKET_TEMPLATE = 0;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getNewTicketEmailTemplate(Ticket $ticket): Email
    {
        $email = $ticket->getAuthor()->getEmail();
        return (new Email())
            ->from($this->container->getParameter('helpdesk_email'))
            ->to($email)
            ->subject(sprintf('Your ticket with id %d has been added', $ticket->getId()))
            ->text('Your ticket has been added and waiting for answer!');
    }
}