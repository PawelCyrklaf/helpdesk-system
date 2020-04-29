<?php

namespace App\Event;

use App\Entity\Ticket;
use Symfony\Contracts\EventDispatcher\Event;

class TicketEvent extends Event
{
    public const NEW_TICKET = 'ticket.added';
    public const TICKET_CLOSED = 'ticket.closed';

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function getTicket()
    {
        return $this->ticket;
    }
}