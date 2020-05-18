<?php

namespace App\Controller;

use App\Service\SearchService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractFOSRestController
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @Rest\Post("/search", name="search")
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $results = $this->searchService->search($request);
        return $this->view(array('results' => $results), Response::HTTP_OK);
    }
}
