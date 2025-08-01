<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\TemplateRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            openapiContext: [
                'summary' => 'Api to get the list of the available template in frontEnd',
            ]
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['Template:read']],
    denormalizationContext: ['groups' => ['Template:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact'])]
#[ApiFilter(SearchFilter::class, properties: ['category.id' => 'exact'])]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Template:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Template:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Template:read'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['Template:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Template:read'])]
    private ?array $props = null;

    #[ORM\ManyToOne(inversedBy: 'templates')]
    #[Groups(['Template:read'])]
    private ?Category $category = null;

    #[ORM\OneToOne(inversedBy: 'template', cascade: ['persist', 'remove'])]
    #[Groups(['Template:read'])]
    private ?Image $image = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getProps(): ?array
    {
        return $this->props;
    }

    public function setProps(?array $props): static
    {
        $this->props = $props;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }
}
