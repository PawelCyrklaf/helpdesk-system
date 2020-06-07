<?php

namespace App\EventListener;

use App\Entity\Reply;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class DoctrineListener
{
    private LoggerInterface $logger;
    private Security $security;

    public function __construct(LoggerInterface $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index('persist', $args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index('update', $args);
    }

    public function index(string $method, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Ticket) {
            $this->logger->info(sprintf('Ticket with id %d has been ' . $method . ' for user %s', $entity->getId(), $this->security->getUser()->getUsername()));
        } elseif ($entity instanceof Reply) {
            $this->logger->info(sprintf('Reply with id %d has been ' . $method . ' for user with id %d', $entity->getId(), $this->security->getUser()->getUsername()));
        } elseif ($entity instanceof User) {
            $this->logger->info(sprintf('User with id %d has been ' . $method, $entity->getId()));
        }
    }
}