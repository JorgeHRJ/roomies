<?php

namespace App\Entity;

use App\Library\Entity\BlameableEntityInterface;
use App\Library\Traits\Entity\BlameableTrait;
use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\Table(name="task")
 * @ORM\EntityListeners({"App\Listener\BlameableEntityListener"})
 */
class Task implements BlameableEntityInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    const NORMAL_TYPE = 0;
    const PERIODIC_TYPE = 1;
    const PER_TURN_TYPE = 2;

    const PENDING_STATUS = 0;
    const DONE_STATUS = 1;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="task_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="task.title.not_blank")
     * @Assert\Length(max=128, maxMessage="task.title.length")
     *
     * @ORM\Column(name="task_title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="task_description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="task_type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var int|null
     *
     * @ORM\Column(name="task_status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="task_assigned", referencedColumnName="user_id", nullable=true)
     */
    private $assigned;

    /**
     * @var Home|null
     *
     * @ORM\ManyToOne(targetEntity=Home::class)
     * @ORM\JoinColumn(name="task_home", referencedColumnName="home_id", nullable=false)
     */
    private $home;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="task_scheduled_at", type="datetime", nullable=false)
     */
    private $scheduledAt;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="task_created_by", referencedColumnName="user_id", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="task_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="task_modified_at", type="datetime", nullable=true)
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
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssigned(): ?User
    {
        return $this->assigned;
    }

    /**
     * @param User|null $assigned
     * @return $this
     */
    public function setAssigned(?User $assigned): self
    {
        $this->assigned = $assigned;

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

    public function setBlamed(User $user): void
    {
        $this->setCreatedBy($user);
    }
}
