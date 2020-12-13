<?php

namespace App\Entity;

use App\Library\Entity\BlameableEntityInterface;
use App\Library\Traits\Entity\BlameableTrait;
use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpenseRepository")
 * @ORM\Table(name="expense")
 * @ORM\EntityListeners({"App\Listener\BlameableEntityListener"})
 */
class Expense implements BlameableEntityInterface
{
    use TimestampableTrait;
    use BlameableTrait;
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="expense_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var Home|null
     *
     * @ORM\ManyToOne(targetEntity=Home::class)
     * @ORM\JoinColumn(name="expense_home", referencedColumnName="home_id", nullable=false)
     */
    private $home;

    /**
     * @var string|null
     *
     * @ORM\Column(name="expense_amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="expense.title.not_blank")
     * @Assert\Length(max=128, maxMessage="expense.title.length")
     *
     * @ORM\Column(name="expense_title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="expense_description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="expense_paid_by", referencedColumnName="user_id", nullable=false)
     */
    private $paidBy;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="expense_created_by", referencedColumnName="user_id", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="expense_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="expense_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @var Collection|null
     *
     * @ORM\ManyToMany(targetEntity=ExpenseTag::class, mappedBy="expenses")
     * @ORM\JoinTable(name="expensetag_expense",
     *      joinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="expense_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="expensetag_id", referencedColumnName="expensetag_id")}
     * )
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=ExpenseUser::class, mappedBy="expense", cascade={"persist", "remove"})
     */
    private $expenseUsers;

    /**
     * @var File|null
     *
     * @ORM\OneToOne(targetEntity=File::class, inversedBy="expense", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="expense_file", referencedColumnName="file_id")
     */
    private $file;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->expenseUsers = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Home|null
     */
    public function getHome(): ?Home
    {
        return $this->home;
    }

    /**
     * @param Home|null $home
     * @return $this
     */
    public function setHome(?Home $home): self
    {
        $this->home = $home;

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
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|ExpenseTag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param ExpenseTag $tag
     * @return $this
     */
    public function addTag(ExpenseTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addExpense($this);
        }

        return $this;
    }

    /**
     * @param ExpenseTag $tag
     * @return $this
     */
    public function removeTag(ExpenseTag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removeExpense($this);
        }

        return $this;
    }

    /**
     * @return Collection|ExpenseUser[]
     */
    public function getExpenseUsers(): Collection
    {
        return $this->expenseUsers;
    }

    /**
     * @param ExpenseUser $expenseUser
     * @return $this
     */
    public function addExpenseUser(ExpenseUser $expenseUser): self
    {
        if (!$this->expenseUsers->contains($expenseUser)) {
            $this->expenseUsers[] = $expenseUser;
            $expenseUser->setExpense($this);
        }

        return $this;
    }

    /**
     * @param ExpenseUser $expenseUser
     * @return $this
     */
    public function removeExpenseUser(ExpenseUser $expenseUser): self
    {
        if ($this->expenseUsers->contains($expenseUser)) {
            $this->expenseUsers->removeElement($expenseUser);
            // set the owning side to null (unless already changed)
            if ($expenseUser->getExpense() === $this) {
                $expenseUser->setExpense(null);
            }
        }

        return $this;
    }

    public function setBlamed(User $user): void
    {
        $this->setCreatedBy($user);
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return $this
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getPaidBy(): ?User
    {
        return $this->paidBy;
    }

    /**
     * @param User|null $paidBy
     * @return $this
     */
    public function setPaidBy(?User $paidBy): self
    {
        $this->paidBy = $paidBy;

        return $this;
    }
}
