<?php

namespace App\Controller\App;

use App\Service\ExpenseTagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/expensetag", name="expensetag_")
 */
class ExpenseTagController extends AbstractController
{
    /** @var ExpenseTagService */
    private $expenseTagService;

    public function __construct(ExpenseTagService $expenseTagService)
    {
        $this->expenseTagService = $expenseTagService;
    }

    /**
     * @Route("/_search", name="search")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        $result = $this->expenseTagService->search($data['query']);

        return new JsonResponse($result, JsonResponse::HTTP_OK);
    }
}
