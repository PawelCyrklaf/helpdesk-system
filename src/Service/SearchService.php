<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchService
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function search(Request $request)
    {
        $query = json_decode($request->getContent(), true);
        $finder = $this->container->get('fos_elastica.finder.app_ticket.ticket');

        return $finder->find($query['query']);
    }
}