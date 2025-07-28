<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Repository\GlobalSeoRepository;
use App\Entity\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GlobalSeoRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Api to save  the global seo',
                'security' => [['bearerAuth' => []]],
            ]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['GlobalSeo:read']],
    denormalizationContext: ['groups' => ['GlobalSeo:write']]
)]
class GlobalSeo
{

    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['GlobalSeo:read', 'Model:read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Model>
     */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'globalSeo', orphanRemoval: true)]
    #[Groups(['GlobalSeo:write'])]
    private Collection $model;

    #[ORM\Column]
    #[Groups(['GlobalSeo:read','GlobalSeo:write', 'Model:write', 'Model:read'])]
    private array $formValue = [];

    #[ORM\Column]
    #[Groups(['GlobalSeo:read','GlobalSeo:write', 'Model:write', 'Model:read'])]
    private array $metadata = [];

    public function __construct()
    {
        $this->model = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Model>
     */
    public function getModel(): Collection
    {
        return $this->model;
    }

    public function addModel(Model $model): static
    {
        if (!$this->model->contains($model)) {
            $this->model->add($model);
            $model->setGlobalSeo($this);
        }

        return $this;
    }

    public function removeModel(Model $model): static
    {
        if ($this->model->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getGlobalSeo() === $this) {
                $model->setGlobalSeo(null);
            }
        }

        return $this;
    }

    public function getFormValue(): array
    {
        return $this->formValue;
    }

    public function setFormValue(array $formValue): static
    {
        $this->formValue = $formValue;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }
}
