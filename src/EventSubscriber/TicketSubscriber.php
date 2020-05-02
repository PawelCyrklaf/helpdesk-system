<?php

namespace App\EventSubscriber;

use App\Event\TicketClosedEvent;
use App\Event\TicketCreatedEvent;
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

    public static function getSubscribedEvents()
    {
        return [
            TicketCreatedEvent::class => ['newTicket'],
            TicketClosedEvent::class => ['closedTicket'],
        ];
    }

    public function newTicket(TicketCreatedEvent $ticketEvent)
    {
        $ticket = $ticketEvent->getTicket();
        if ($ticket) {
            $this->mailService->send($ticket, EmailTemplateService::NEW_TICKET_TEMPLATE);
        }
    }

    public function closedTicket(TicketClosedEvent $ticketEvent)
    {
        $ticket = $ticketEvent->getTicket();

        if ($ticket) {
            // TODO Add implementation: send email to customer with information about their ticket is closed
        }
    }
}