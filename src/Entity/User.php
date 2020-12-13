<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="user.email.unique"
 * )
 */
class User implements UserInterface
{
    use TimestampableTrait;

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLES = [self::ROLE_ADMIN, self::ROLE_USER];

    const DISABLED_STATUS = 0;
    const ENABLED_STATUS = 1;

    /**
     * @var int|null
     *
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_uuid", type="string", length=256, unique=true)
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="user.email.not_blank")
     * @Assert\Length(max=256, maxMessage="user.email.length")
     *
     * @ORM\Column(name="user_email", type="string", length=256)
     */
    private $email;

    /**
     * @var string|null
     *
     * @Assert\Choice(choices=User::ROLES, message="role.choices")
     *
     * @ORM\Column(name="user_role", type="string", nullable=false)
     */
    private $role;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_password", type="string", nullable=false)
     */
    private $password;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_status", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="user.name.not_blank")
     * @Assert\Length(max=32, maxMessage="user.name.length")
     *
     * @ORM\Column(name="user_name", type="string", length=32)
     */
    private $name;

    /**
     * @var Collection|null
     *
     * @ORM\ManyToMany(targetEntity=Home::class, mappedBy="users")
     * @ORM\JoinTable(name="home_user",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="home_id", referencedColumnName="home_id")}
     * )
     */
    private $homes;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="user_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="user_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\OneToMany(targetEntity=ExpenseUser::class, mappedBy="user")
     */
    private $expenseUsers;

    public function __construct()
    {
        $this->homes = new ArrayCollection();
        $this->expenseUsers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return $this
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @see UserInterface
     * @return array
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Home[]
     */
    public function getHomes(): Collection
    {
        return $this->homes;
    }

    /**
     * @param Home $home
     * @return $this
     */
    public function addHome(Home $home): self
    {
        if (!$this->homes->contains($home)) {
            $this->homes[] = $home;
            $home->addUser($this);
        }

        return $this;
    }

    /**
     * @param Home $home
     * @return $this
     */
    public function removeHome(Home $home): self
    {
        if ($this->homes->contains($home)) {
            $this->homes->removeElement($home);
            $home->removeUser($this);
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
            $expenseUser->setUser($this);
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
            if ($expenseUser->getUser() === $this) {
                $expenseUser->setUser(null);
            }
        }

        return $this;
    }
}
