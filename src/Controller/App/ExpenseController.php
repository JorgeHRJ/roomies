<?php

namespace App\Controller\App;

use App\Library\Controller\BaseController;
use App\Service\ExpenseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route({
 *     "es": "/gastos",
 *     "en": "/expenses"
 * }, name="expense_")
 */
class ExpenseController extends BaseController
{
    const LIST_LIMIT = 20;

    /** @var ExpenseService */
    private $expenseService;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(ExpenseService $expenseService, TranslatorInterface $translator)
    {
        $this->expenseService = $expenseService;
        $this->translator = $translator;
    }

    /**
     * @Route({
     *     "es": "/",
     *     "en": "/"
     * }, name="index")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        list($page, $limit, $sort, $dir, $filter) = $this->handleIndexRequest($request, self::LIST_LIMIT);
        $expenses = $this->expenseService->getAll($filter, $page, $limit, $sort, $dir);

        $paginationData = $this->getPaginationData($request, $expenses, $page, $limit);

        return $this->render('app/expense/index.html.twig', array_merge(
            $expenses,
            [
                'sort' => $request->query->get('sort'),
                'dir' => $request->query->get('dir'),
                'paginationData' => $paginationData,
                'params' => $request->query->all(),
            ]
        ));
    }
}
