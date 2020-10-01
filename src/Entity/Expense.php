<?php

namespace App\Entity;

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
 */
class Expense
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
     * @var Collection|null
     *
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="expenses")
     * @ORM\JoinTable(name="expense_user",
     *      joinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="expense_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")}
     * )
     */
    private $users;

    /**
     * @var Collection|null
     *
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="expense")
     */
    private $files;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->files = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setExpense($this);
        }

        return $this;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getExpense() === $this) {
                $file->setExpense(null);
            }
        }

        return $this;
    }
}