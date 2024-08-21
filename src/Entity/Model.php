<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['Page:read']],
    denormalizationContext: ['groups' => ['Page:write']]
)]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Page:read','Page:write','Image:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Page:read','Page:write','Image:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Page:read','Page:write'])]
    private ?array $props = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Page:read','Page:write'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'model')]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Page:read','Page:write','Image:read'])]
    private ?string $themeColor = 'default';

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setModel($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getModel() === $this) {
                $image->setModel(null);
            }
        }

        return $this;
    }

    public function getThemeColor(): ?string
    {
        return $this->themeColor;
    }

    public function setThemeColor(?string $themeColor): static
    {
        $this->themeColor = $themeColor;

        return $this;
    }
}