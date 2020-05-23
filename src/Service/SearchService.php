<?php

namespace App\Service;

use Elastica\Query\BoolQuery;
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

        $boolQuery = new BoolQuery();
        $subjectQuery = new \Elastica\Query\Match();
        $subjectQuery->setFieldQuery('subject', $query['query']);
        $subjectQuery->setFieldFuzziness('subject', 3);
        $boolQuery->addMust($subjectQuery);

        return $finder->find($boolQuery);
    }
}