<?php

namespace App\ApiResource\DataTransformer;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class PageTransformer implements DataTransformerInterface
{

    public function __construct(private readonly  EntityManagerInterface $entityManager)
    {
    }

    public function transform(mixed $value): mixed
    {
        dd($value);
        if($value instanceof Page) {
            return $value?->getId();
        }
        return null;
    }

    public function reverseTransform(mixed $value): mixed
    {
        dd($value);
        if (!$value) {
            return null;
        }

        return $this->entityManager->getRepository(Page::class)->find($value);
    }
}