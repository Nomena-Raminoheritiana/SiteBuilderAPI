<?php

namespace App\Entity;

use App\Repository\BusinessCustomerTargetRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BusinessCustomerTargetRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            read: true,
            name: 'business_customer_target',
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Api to get the list of the type of customer targets',
                'security' => [['bearerAuth' => []]],
            ]
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['BusinessCustomerTarget:read']],
    denormalizationContext: ['groups' => ['BusinessCustomerTarget:write']]
)]
class BusinessCustomerTarget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['BusinessCustomerTarget:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['BusinessCustomerTarget:read'])]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    #[Groups(['BusinessCustomerTarget:read'])]
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
