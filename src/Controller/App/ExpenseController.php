<?php

namespace App\Controller\App;

use App\Entity\Expense;
use App\Entity\File;
use App\Form\ExpenseType;
use App\Library\Controller\BaseController;
use App\Messenger\BlurImage\BlurImageMessage;
use App\Service\ContextService;
use App\Service\ExpenseService;
use App\Service\ExpenseTagService;
use App\Service\FileService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
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

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(
        ExpenseService $expenseService,
        ExpenseTagService $expenseTagService,
        FileService $fileService,
        UserService $userService,
        ContextService $contextService,
        TranslatorInterface $translator,
        MessageBusInterface $messageBus
    ) {
        $this->expenseService = $expenseService;
        $this->expenseTagService = $expenseTagService;
        $this->fileService = $fileService;
        $this->userService = $userService;
        $this->contextService = $contextService;
        $this->translator = $translator;
        $this->messageBus = $messageBus;
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
        $debts = $this->expenseService->getDebts();

        $paginationData = $this->getPaginationData($request, $expenses, $page, $limit);

        return $this->render('app/expense/index.html.twig', array_merge(
            $expenses,
            [
                'sort' => $request->query->get('sort'),
                'dir' => $request->query->get('dir'),
                'paginationData' => $paginationData,
                'params' => $request->query->all(),
                'debts' => $debts
            ]
        ));
    }

    /**
     * @Route({
     *     "es": "/{id}",
     *     "en": "/{id}"
     * }, requirements={"id"="\d+"}, name="detail")
     *
     * @param int $id
     * @return Response
     */
    public function detail(int $id): Response
    {
        $expense = $this->getExpenseFromRequest($id);

        $imageId = $expense->getFile() instanceof File ? $expense->getFile()->getId() : '0';
        $this->messageBus->dispatch(new BlurImageMessage($imageId));

        return $this->render('app/expense/detail.html.twig', ['expense' => $expense]);
    }

    /**
     * @Route({
     *     "es": "/eliminar/{id}",
     *     "en": "/remove/{id}"
     * }, requirements={"id"="\d+"}, name="remove")
     *
     * @param int $id
     * @return Response
     */
    public function remove(int $id): Response
    {
        $expense = $this->getExpenseFromRequest($id);
        try {
            $this->expenseService->remove($expense);

            $this->addFlash(
                'app_success',
                $this->translator->trans('expense.remove.success_message', [], 'expense')
            );
        } catch (\Exception $e) {
            $this->addFlash(
                'app_error',
                $this->translator->trans('expense.remove.error_message', [], 'expense')
            );
        }

        return $this->redirectToRoute('app_expense_index');
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

    /**
     * @param int $id
     * @return Expense
     */
    private function getExpenseFromRequest(int $id): Expense
    {
        $home = $this->contextService->getHome();

        $expense = $this->expenseService->getByIdAndHome($home, $id);
        if (!$expense instanceof Expense) {
            throw new NotFoundHttpException();
        }

        return $expense;
    }
}
