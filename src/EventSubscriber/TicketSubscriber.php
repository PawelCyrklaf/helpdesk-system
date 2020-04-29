<?php

namespace App\EventSubscriber;

use App\Event\TicketEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TicketSubscriber implements EventSubscriberInterface
{
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
            // TODO Add implementation: send email to support with information about new ticket
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