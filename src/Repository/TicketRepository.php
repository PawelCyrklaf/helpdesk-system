<?php

namespace App\Repository;

use App\Contracts\BaseRepositoryInterface;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository implements BaseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function save($object): void
    {
        $this->_em->persist($object);
        $this->_em->flush();
    }

    public function remove($object): void
    {
        $this->_em->remove($object);
        $this->_em->flush();
    }

    public function update(): void
    {
        $this->_em->flush();
    }
}
