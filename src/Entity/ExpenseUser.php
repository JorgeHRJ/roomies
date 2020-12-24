<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\ExpenseUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExpenseUserRepository::class)
 * @ORM\Table(name="expense_user")
 */
class ExpenseUser
{
    use TimestampableTrait;

    const PENDING_STATUS = 0;
    const PAID_STATUS = 1;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="expenseuser_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var Expense|null
     *
     * @ORM\ManyToOne(targetEntity=Expense::class, inversedBy="expenseUsers")
     * @ORM\JoinColumn(name="expenseuser_expense", referencedColumnName="expense_id", nullable=false)
     */
    private $expense;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="expenseUsers")
     * @ORM\JoinColumn(name="expenseuser_user", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    /**
     * @var string|null
     *
     * @ORM\Column(name="expenseuser_amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="expenseuser_status", type="integer")
     */
    private $status;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     *
     * @ORM\Column(name="expenseuser_paid_at", type="datetime", nullable=true)
     */
    private $paidAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="expenseuser_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="expenseuser_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Expense|null
     */
    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    /**
     * @param Expense|null $expense
     * @return $this
     */
    public function setExpense(?Expense $expense): self
    {
        $this->expense = $expense;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    /**
     * @param \DateTimeInterface|null $paidAt
     * @return $this
     */
    public function setPaidAt(?\DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }
}
