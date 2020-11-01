<?php

namespace App\Listener;

use App\Entity\User;
use App\Library\Entity\BlameableEntityInterface;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Security\Core\Security;

class BlameableEntityListener
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @PrePersist
     *
     * @param BlameableEntityInterface $entity
     */
    public function prePersistHandler(BlameableEntityInterface $entity): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $entity->setBlamed($user);
        }
    }

    /**
     * @PreUpdate
     *
     * @param BlameableEntityInterface $entity
     */
    public function preUpdateHandler(BlameableEntityInterface $entity): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $entity->setBlamed($user);
        }
    }
}
