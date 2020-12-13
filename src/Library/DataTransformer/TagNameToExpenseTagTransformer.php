<?php

namespace App\Library\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagNameToExpenseTagTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($array)
    {
        if (null === $array) {
            return [];
        }

        if (!\is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if (empty($array)) {
            return [];
        }

        return array_combine($array, $array);
    }

    public function reverseTransform($array)
    {
        if (null === $array) {
            return [];
        }

        if (!\is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return array_combine($array, $array);
    }
}
