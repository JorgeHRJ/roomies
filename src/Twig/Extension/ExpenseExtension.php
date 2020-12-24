<?php

namespace App\Twig\Extension;

use App\Entity\ExpenseUser;
use App\Entity\User;
use App\Service\ContextService;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExpenseExtension extends AbstractExtension
{
    /** @var ContextService */
    private $contextService;

    /** @var Security */
    private $security;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(ContextService $contextService, Security $security, TranslatorInterface $translator)
    {
        $this->contextService = $contextService;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_debts_summary', [$this, 'getDebtsSummary']),
            new TwigFunction('get_debts_explained', [$this, 'getDebtsExplained']),
            new TwigFunction('get_expense_user_status', [$this, 'getExpenseUserStatus'])
        ];
    }

    /**
     * @param array $debts
     * @return array
     */
    public function getDebtsSummary(array $debts): array
    {
        $ownDebts = $this->getOwnDebts($debts);
        $currency = $this->contextService->getHome()->getCurrency();

        $summary = ['to_you' => 0, 'to_them' => 0];

        foreach ($ownDebts as $debt) {
            if ($debt < 0) {
                $summary['to_them'] -= $debt['amount'];
            }

            if ($debt > 0) {
                $summary['to_you'] += $debt['amount'];
            }
        }

        $summary['to_them'] = $summary['to_them'] === 0
            ? $this->translator->trans('expense.summary.to_them_total_none', [], 'expense')
            : sprintf(
                '%s %s %s',
                $this->translator->trans('expense.summary.to_them_total', [], 'expense'),
                $summary['to_them'],
                $currency
            );

        $summary['to_you'] = $summary['to_you'] === 0
            ? $this->translator->trans('expense.summary.to_you_total_none', [], 'expense')
            : sprintf(
                '%s %s %s',
                $this->translator->trans('expense.summary.to_you_total', [], 'expense'),
                $summary['to_you'],
                $currency
            );

        return $summary;
    }

    public function getDebtsExplained(array $debts): array
    {
        $ownDebts = $this->getOwnDebts($debts);
        $currency = $this->contextService->getHome()->getCurrency();

        $toYouLabel = $this->translator->trans('expense.summary.to_you', [], 'expense');
        $toHimHerLabel = $this->translator->trans('expense.summary.to_him_her', [], 'expense');
        $okLabel = $this->translator->trans('expense.summary.nothing_pending', [], 'expense');

        $explainedItems = [];
        foreach ($ownDebts as $debt) {
            $label = $okLabel;
            if ($debt['amount'] > 0) {
                $label = $toYouLabel;
            }

            if ($debt['amount'] < 0) {
                $label = $toHimHerLabel;
            }

            $explained = $label === $okLabel
                ? sprintf('%s: %s', $debt['user_name'], $label)
                : sprintf('%s: %s %s %s', $debt['user_name'], $label, $debt['amount'], $currency);
            $explainedItems[] = $explained;
        }

        return $explainedItems;
    }

    /**
     * @param ExpenseUser $expenseUser
     * @return array
     * @throws \Exception
     */
    public function getExpenseUserStatus(ExpenseUser $expenseUser): array
    {
        switch ($expenseUser->getStatus()) {
            case ExpenseUser::PENDING_STATUS:
                return [
                    'label' => $this->translator->trans('expense_user.status.pending', [], 'expense'),
                    'badge_class' => 'danger'
                ];
            case ExpenseUser::PAID_STATUS:
                return [
                    'label' => $this->translator->trans('expense_user.status.paid', [], 'expense'),
                    'badge_class' => 'success'
                ];
            default:
                throw new \Exception('Not handled status!');
        }
    }

    /**
     * @param array $debts
     * @return array
     */
    private function getOwnDebts(array $debts): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException('Not allowed');
        }

        return isset($debts[$user->getId()]) ? $debts[$user->getId()] : [];
    }
}
