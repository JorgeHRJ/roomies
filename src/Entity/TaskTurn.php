<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskTurnRepository")
 * @ORM\Table(name="taskturn")
 */
class TaskTurn
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="taskturn_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="taskturn_date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var Task|null
     *
     * @ORM\ManyToOne(targetEntity=Task::class)
     * @ORM\JoinColumn(name="taskturn_task", referencedColumnName="task_id", nullable=false)
     */
    private $task;

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
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     * @return $this
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     * @return $this
     */
    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }
}
