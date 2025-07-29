<?php

namespace App\Entity;

use App\Repository\BusinessGoalRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BusinessGoalRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            read: true,
            name: 'business_goal',
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Api to get the list of the business goal',
                'security' => [['bearerAuth' => []]],
            ]
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['BusinessGoal:read']],
    denormalizationContext: ['groups' => ['BusinessGoal:write']]
)]
class BusinessGoal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['BusinessGoal:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['BusinessGoal:read'])]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    #[Groups(['BusinessGoal:read'])]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
