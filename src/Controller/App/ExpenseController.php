<?php

namespace App\Controller\App;

use App\Entity\Expense;
use App\Entity\File;
use App\Form\ExpenseType;
use App\Library\Controller\BaseController;
use App\Service\ContextService;
use App\Service\ExpenseService;
use App\Service\ExpenseTagService;
use App\Service\FileService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    /** @var ExpenseTagService */
    private $expenseTagService;

    /** @var UserService */
    private $userService;

    /** @var ContextService */
    private $contextService;

    /** @var FileService */
    private $fileService;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        ExpenseService $expenseService,
        ExpenseTagService $expenseTagService,
        FileService $fileService,
        UserService $userService,
        ContextService $contextService,
        TranslatorInterface $translator
    ) {
        $this->expenseService = $expenseService;
        $this->expenseTagService = $expenseTagService;
        $this->fileService = $fileService;
        $this->userService = $userService;
        $this->contextService = $contextService;
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

    /**
     * @Route({
     *     "es": "/nuevo",
     *     "en": "/new"
     * }, name="new")
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $expense = new Expense();
        $home = $this->contextService->getHome();
        $homeUsers = $this->userService->getByHome($home);

        $form = $this->createForm(ExpenseType::class, $expense, ['home_users' => $homeUsers]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('app/expense/new.html.twig', [
                    'form' => $form->createView(),
                    'home_users' => $homeUsers
                ]);
            }

            try {
                $expense->setHome($home);

                 $expense = $this->expenseService->create($expense);
                 $this->expenseTagService->handleExpenseTags($expense, array_values($form->get('tags')->getData()));

                $uploadedFile = $form->get('file')->getData();
                if ($uploadedFile instanceof UploadedFile) {
                    $file = $this->fileService->handleUpload($uploadedFile, File::HOME_EXPENSE_ORIGIN);
                    $file->setExpense($expense);

                    $this->fileService->update($file);
                }

                $this->addFlash(
                    'app_success',
                    $this->translator->trans('expense.form.new.success_message', [], 'expense')
                );

                return $this->redirectToRoute('app_expense_index');
            } catch (\Exception $e) {
                $this->addFlash(
                    'app_error',
                    $this->translator->trans('expense.form.new.error_message', [], 'expense')
                );
            }
        }

        return $this->render('app/expense/new.html.twig', [
            'form' => $form->createView(),
            'home_users' => $homeUsers
        ]);
    }
}
