<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Controller\Model\CreateModelController;
use App\ApiResource\Controller\Model\GetModelsByUserUuidController;
use App\ApiResource\Controller\Model\SyncDataController;
use App\ApiResource\Dto\Input\Model\ModelSyncInput;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            // security: "is_granted('ROLE_ADMIN')",
            // read: false,
            // controller: GetModelByUserUuidController::class,
            openapiContext: [
                'summary' => 'Api to get a model for the current user connected',
                // 'security' => [['bearerAuth' => []]],
            ]
        ),
        new GetCollection(
            uriTemplate: '/models',
            read: false,
            name: 'models_by_user',
            controller: GetModelsByUserUuidController::class,
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Api to get the list of the models of the user connected',
                'security' => [['bearerAuth' => []]],
            ]
        ),
        new GetCollection(
            uriTemplate: '/models/getBySlug/{slug}',
            uriVariables: [
                'slug'=>  new Link(
                    fromClass: Model::class,
                    identifiers: ['slug'],
                    parameterName: 'slug'
                )
            ],
            read: true,
            name: 'models_by_slug',
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Api to get the list of the models of the user connected',
                'security' => [['bearerAuth' => []]],
            ]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            controller: CreateModelController::class,
            openapiContext: [
                'summary' => 'Api to save model of the user connected',
                'security' => [['bearerAuth' => []]],
            ]
        ),
        new Post(
            read: false,
            write: false,
            uriTemplate: '/models/syncData',
            input: ModelSyncInput::class,
            security: "is_granted('ROLE_ADMIN')",
            controller: SyncDataController::class,
            denormalizationContext: ['groups' => ['Model:sync-write']],
            openapiContext: [
                'summary' => 'Api to sync data',
                'security' => [['bearerAuth' => []]],
            ]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['Model:read']],
    denormalizationContext: ['groups' => ['Model:write']]
)]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Model:read','Model:write','Image:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Model:read','Model:write','Image:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Model:read','Model:write'])]
    private ?array $props = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Model:read','Model:write'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'model')]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Model:read','Model:write','Image:read'])]
    private ?string $themeColor = 'default';

    #[ORM\ManyToOne(inversedBy: 'modeles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
