<?php

namespace App\Library\DataTransformer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToNumberTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (user) to a string (number).
     *
     * @param User|null $user
     * @return string
     */
    public function transform($user): string
    {
        if (!$user instanceof User) {
            return '';
        }

        return (string) $user->getId();
    }

    /**
     * Transforms a integer (number) to an object (user).
     *
     * @param string $userId
     * @return User|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($userId): ?User
    {
        $user = $this->entityManager->getRepository(User::class)->find((int) $userId);

        if (!$user instanceof User) {
            throw new TransformationFailedException(sprintf(
                'An use with id "%s" does not exist!',
                $userId
            ));
        }

        return $user;
    }
}
