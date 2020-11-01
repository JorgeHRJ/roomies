<?php

namespace App\Entity;

use App\Library\Entity\BlameableEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="file")
 * @ORM\EntityListeners({"App\Listener\BlameableEntityListener"})
 */
class File implements BlameableEntityInterface
{
    const IMAGE_TYPE = 'image';
    const DOC_TYPE = 'document';
    const TYPES = [self::IMAGE_TYPE, self::DOC_TYPE];

    const HOME_AVATAR_ORIGIN = 'avatar';
    const HOME_CLOUD_ORIGIN = 'cloud';
    const HOME_EXPENSE_ORIGIN = 'expense';

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="file_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="file.name.not_blank")
     * @Assert\Length(max=128, maxMessage="file.name.length")
     *
     * @ORM\Column(name="file_name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="file_path", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var string|null
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="file_filename", type="string", length=128, nullable=false)
     */
    private $filename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_type", type="string", length=8, nullable=false)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_extension", type="string", length=8, nullable=false)
     */
    private $extension;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_origin", type="string", length=32, nullable=false)
     */
    private $origin;

    /**
     * @var Home|null
     *
     * @ORM\ManyToOne(targetEntity=Home::class, inversedBy="files")
     * @ORM\JoinColumn(name="file_home", referencedColumnName="home_id", nullable=false)
     */
    private $home;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="file_uploaded_at", type="datetime", nullable=false)
     */
    private $uploadedAt;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="file_uploaded_by", referencedColumnName="user_id", nullable=false)
     */
    private $uploadedBy;

    /**
     * @var Expense|null
     *
     * @ORM\ManyToOne(targetEntity=Expense::class, inversedBy="files")
     * @ORM\JoinColumn(name="file_expense", referencedColumnName="expense_id", nullable=true)
     */
    private $expense;

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
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return $this
     */
    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

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
     * @return \DateTimeInterface|null
     */
    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    /**
     * @param \DateTimeInterface $uploadedAt
     * @return $this
     */
    public function setUploadedAt(\DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    /**
     * @param User|null $uploadedBy
     * @return $this
     */
    public function setUploadedBy(?User $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
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

    public function setBlamed(User $user): void
    {
        $this->setUploadedBy($user);
    }
}
