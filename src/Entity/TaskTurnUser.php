<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskTurnUserRepository")
 * @ORM\Table(name="taskturnuser")
 */
class TaskTurnUser
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="taskturnuser_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var TaskTurn|null
     *
     * @ORM\ManyToOne(targetEntity=TaskTurn::class)
     * @ORM\JoinColumn(name="taskturnuser_turn", referencedColumnName="taskturn_id", nullable=false)
     */
    private $turn;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="taskturnuser_user", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="taskturn_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="taskturn_modified_at", type="datetime", nullable=true)
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
     * @return TaskTurn|null
     */
    public function getTurn(): ?TaskTurn
    {
        return $this->turn;
    }

    /**
     * @param TaskTurn|null $turn
     * @return $this
     */
    public function setTurn(?TaskTurn $turn): self
    {
        $this->turn = $turn;

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
}
