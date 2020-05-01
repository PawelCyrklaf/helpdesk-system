<?php

namespace App\EventSubscriber;

use App\Event\TicketEvent;
use App\Service\EmailTemplateService;
use App\Service\MailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TicketSubscriber implements EventSubscriberInterface
{
    private MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public static function getSubscribedEvents()
    {
        return [
            TicketEvent::NEW_TICKET => ['newTicket'],
            TicketEvent::TICKET_CLOSED => ['closedTicket'],
        ];
    }

    public function newTicket(TicketEvent $ticketEvent)
    {
        $ticket = $ticketEvent->getTicket();
        if ($ticket) {
            $this->mailService->send($ticket, EmailTemplateService::NEW_TICKET_TEMPLATE);
        }
    }

    public function closedTicket(TicketEvent $ticketEvent)
    {
        $ticket = $ticketEvent->getTicket();

        if ($ticket) {
            // TODO Add implementation: send email to customer with information about their ticket is closed
        }
    }
}