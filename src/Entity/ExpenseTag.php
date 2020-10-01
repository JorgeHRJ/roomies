<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpenseTagRepository")
 * @ORM\Table(name="expensetag")
 */
class ExpenseTag
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="expensetag_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="expensetag.name.not_blank")
     * @Assert\Length(max=128, maxMessage="expensetag.name.length")
     *
     * @ORM\Column(name="expensetag_name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var Home|null
     *
     * @ORM\ManyToOne(targetEntity=Home::class)
     * @ORM\JoinColumn(name="expensetag_home", referencedColumnName="home_id", nullable=false)
     */
    private $home;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="expensetag_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="expensetag_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @var Collection|null
     *
     * @ORM\ManyToMany(targetEntity=Expense::class, inversedBy="tags")
     * @ORM\JoinTable(name="expensetag_expense",
     *      joinColumns={@ORM\JoinColumn(name="expensetag_id", referencedColumnName="expensetag_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="expense_id")}
     * )
     */
    private $expenses;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
     * @return Collection|Expense[]
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    /**
     * @param Expense $expense
     * @return $this
     */
    public function addExpense(Expense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses[] = $expense;
        }

        return $this;
    }

    /**
     * @param Expense $expense
     * @return $this
     */
    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->contains($expense)) {
            $this->expenses->removeElement($expense);
        }

        return $this;
    }
}
