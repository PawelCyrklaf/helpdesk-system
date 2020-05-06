<?php

namespace App\EventSubscriber;

use App\Event\TicketClosedEvent;
use App\Event\TicketCreatedEvent;
use App\Event\TicketReplyEvent;
use App\Service\EmailTemplateService;
use App\Service\MailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TicketSubscriber implements EventSubscriberInterface
{
    private MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TicketCreatedEvent::class => ['newTicket'],
            TicketClosedEvent::class => ['closedTicket'],
            TicketReplyEvent::class => ['ticketReply'],
        ];
    }

    public function newTicket(TicketCreatedEvent $ticketEvent): void
    {
        $ticket = $ticketEvent->getTicket();
        if ($ticket) {
            $this->mailService->send($ticket, EmailTemplateService::NEW_TICKET_TEMPLATE);
        }
    }

    public function closedTicket(TicketClosedEvent $ticketEvent): void
    {
        $ticket = $ticketEvent->getTicket();

        if ($ticket) {
            $this->mailService->send($ticket, EmailTemplateService::TICKET_CLOSED_TEMPLATE);
        }
    }

    public function ticketReply(TicketReplyEvent $ticketEvent): void
    {
        $ticket = $ticketEvent->getTicket();

        if ($ticket) {
            $this->mailService->send($ticket, EmailTemplateService::TICKET_REPLY_TEMPLATE);
        }
    }
}