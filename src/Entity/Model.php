<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Controller\Model\CreateModelController;
use App\ApiResource\Controller\Model\GetModelsByParentIdController;
use App\ApiResource\Controller\Model\GetModelsByUserUuidController;
use App\ApiResource\Controller\Model\GetModelByUserUuidController;
use App\ApiResource\Controller\Model\MovePropsToPublishedAndCacheController;
use App\ApiResource\Controller\Model\RestorePropsFromCacheController;
use App\ApiResource\Controller\Model\SyncDataController;
use App\ApiResource\Dto\Input\Model\ModelSyncInput;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[UniqueEntity('url', message: 'URL already exist')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/models/compact_Data/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: null,
                    identifiers: ['id'],
                    parameterName: 'id'
                )
            ],
            normalizationContext: ['groups' => ['Model:compact:read']],
            // security: "is_granted('ROLE_ADMIN')",
            // read: false,
            // controller: GetModelByUserUuidController::class,
            openapiContext: [
                'summary' => 'Api to get a model with only the compact data',
                // 'security' => [['bearerAuth' => []]],
            ]
        ),
        new Get(
            // security: "is_granted('ROLE_ADMIN')",
            read: false,
            controller: GetModelByUserUuidController::class,
            openapiContext: [
                'summary' => 'Api to get a model for the current user connected',
                // 'security' => [['bearerAuth' => []]],
            ]
        ),
        new GetCollection(
            uriTemplate: '/models/pagelist/{parentId}',
            uriVariables: [
                'parentId' => new Link(
                    fromClass: null,
                    identifiers: ['parentId'],
                    parameterName: 'parentId'
                )
            ],
            read: false,
            controller: GetModelsByParentIdController::class,
            normalizationContext: ['groups' => ['PageList:read']],
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Api to get the list of the pages of a website',
                'security' => [['bearerAuth' => []]],
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
        new Post(
            uriTemplate: '/models/{id}/publish-props',
            uriVariables: [
                'id' => 'id'
            ],
            controller: MovePropsToPublishedAndCacheController::class,
            name: 'publish_props_to_cache',
            read: false,
            write: false,
            input: false,
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Publishes props to propsPublished and caches them',
                'security' => [['bearerAuth' => []]],
            ],
        ), 
        new Post(
            uriTemplate: '/models/{id}/restore-props',
            uriVariables: [
                'id' => 'id'
            ],
            controller: RestorePropsFromCacheController::class,
            name: 'restore_props_from_cache',
            read: false,
            write: false,
            input: false,
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Restaure les props depuis le cache dans props',
                'security' => [['bearerAuth' => []]],
            ]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['Model:write', 'Model:patch:write']]
        ),
        new Delete(
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
    #[Groups(['Model:read','Model:write','Image:read', 'PageList:read', 'Model:compact:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Model:read','Model:write','Image:read', 'PageList:read', 'Model:compact:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Model:read','Model:write'])]
    private ?array $props = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Model:read', 'PageList:read', 'Model:compact:read'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'model')]
    #[Groups(['Model:read'])]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Model:read','Model:write','Image:read', 'PageList:read', 'Model:compact:read'])]
    private ?string $themeColor = 'default';

    #[ORM\ManyToOne(inversedBy: 'modeles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['Model:read','Model:write', 'PageList:read','Model:compact:read'])]
    private array $seo = [];

    #[ORM\ManyToOne(inversedBy: 'models')]
    #[Groups(['Model:read', 'PageList:read', 'Model:patch:write', 'Model:compact:read'])]
    private ?Status $status = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[Groups(['Model:read','Model:write','Model:compact:read'])]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[Groups(['Model:read','Model:compact:read'])]
    private Collection $children;

    #[ORM\ManyToOne(inversedBy: 'model',cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['Model:read','Model:write', 'Model:compact:read'])]
    private ?GlobalSeo $globalSeo = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Groups(['Model:read', 'Model:write', 'PageList:read', 'Model:patch:write', 'Model:compact:read'])]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?array $propsPublished = null;

    #[ORM\ManyToOne(inversedBy: 'models')]
    #[Groups(['Model:read', 'Model:write', 'PageList:read', 'Model:patch:write', 'Model:compact:read'])]
    private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['PageList:read', 'Model:write', 'Model:patch:write'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {
        if ($this->name) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
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

    public function getSeo(): array
    {
        return $this->seo;
    }

    public function setSeo(array $seo): static
    {
        $this->seo = $seo;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $model): static
    {
        if (!$this->children->contains($model)) {
            $this->children->add($model);
            $model->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $model): static
    {
        if ($this->children->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getParent() === $this) {
                $model->setParent(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getGlobalSeo(): ?GlobalSeo
    {
        return $this->globalSeo;
    }

    public function setGlobalSeo(?GlobalSeo $globalSeo): static
    {
        $this->globalSeo = $globalSeo;

        return $this;
    }

    public function updateGlobalSeo():static
    {
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPropsPublished(): ?array
    {
        return $this->propsPublished;
    }

    public function setPropsPublished(?array $propsPublished): static
    {
        $this->propsPublished = $propsPublished;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
