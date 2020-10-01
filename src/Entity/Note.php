<?php

namespace App\Entity;

use App\Library\Traits\Entity\BlameableTrait;
use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 * @ORM\Table(name="note")
 */
class Note
{
    use TimestampableTrait;
    use BlameableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="note_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="note.title.not_blank")
     * @Assert\Length(max=128, maxMessage="note.title.length")
     *
     * @ORM\Column(name="note_title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="note.content.not_blank")
     *
     * @ORM\Column(name="note_content", type="text", nullable=true, nullable=false)
     */
    private $content;

    /**
     * @var Home|null
     *
     * @ORM\ManyToOne(targetEntity=Home::class)
     * @ORM\JoinColumn(name="note_home", referencedColumnName="home_id", nullable=false)
     */
    private $home;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="note_created_by", referencedColumnName="user_id", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime()
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="note_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\DateTime()
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="note_modified_at", type="datetime", nullable=true)
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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

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
}
