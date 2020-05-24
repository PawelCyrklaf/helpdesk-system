<?php

namespace App\Controller;

use App\Service\SearchService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\Entity\Ticket;

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
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Search"},
     *     summary="Search query",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON data",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="query", type="string", example="lorem ipsum"),
     *          )
     *)
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns search results",
     *     @SWG\Schema(
     *     type="array",
     *     @Model(type=Ticket::class)
     * )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     */
    public function index(Request $request)
    {
        $results = $this->searchService->search($request);
        return $this->view(array('results' => $results), Response::HTTP_OK);
    }
}
