<?php

namespace App\Service;

use App\Entity\Ticket;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    private MailerInterface $mailer;
    private EmailTemplateService $emailTemplateService;

    public function __construct(
        MailerInterface $mailer,
        EmailTemplateService $emailTemplateService
    )
    {
        $this->mailer = $mailer;
        $this->emailTemplateService = $emailTemplateService;
    }

    public function send(Ticket $ticket, int $emailTemplate)
    {
        switch ($emailTemplate) {
            case EmailTemplateService::NEW_TICKET_TEMPLATE:
                $this->mailer->send($this->emailTemplateService->getNewTicketEmailTemplate($ticket));
                break;
            default:
                return false;
        }
    }
}