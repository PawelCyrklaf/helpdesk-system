<?php

namespace App\Service;

use App\Entity\Ticket;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailTemplateService
{
    public const NEW_TICKET_TEMPLATE = 0;
    public const TICKET_CLOSED_TEMPLATE = 1;
    public const TICKET_REPLY_TEMPLATE = 2;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getNewTicketEmailTemplate(Ticket $ticket): TemplatedEmail
    {
        $email = $ticket->getAuthor()->getEmail();
        return (new TemplatedEmail())
            ->from($this->container->getParameter('helpdesk_email'))
            ->to($email)
            ->subject(sprintf('Your ticket with id %d has been added', $ticket->getId()))
            ->htmlTemplate('email/new-ticket.html.twig')
            ->context(array(
                'ticket' => $ticket
            ));
    }

    public function getClosedTicketEmailTemplate(Ticket $ticket): TemplatedEmail
    {
        $email = $ticket->getAuthor()->getEmail();
        return (new TemplatedEmail())
            ->from($this->container->getParameter('helpdesk_email'))
            ->to($email)
            ->subject(sprintf('Your ticket with id %d has been closed', $ticket->getId()))
            ->htmlTemplate('email/closed-ticket.html.twig')
            ->context(array(
                'ticket' => $ticket
            ));
    }

    public function getNewReplyEmailTemplate(Ticket $ticket): TemplatedEmail
    {
        $email = $ticket->getAuthor()->getEmail();
        return (new TemplatedEmail())
            ->from($this->container->getParameter('helpdesk_email'))
            ->to($email)
            ->subject(sprintf('Nwq reply to your ticket with id %d', $ticket->getId()))
            ->htmlTemplate('email/ticket-reply.html.twig')
            ->context(array(
                'ticket' => $ticket
            ));
    }
}