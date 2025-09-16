<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\ApiResource\Controller\Model\CreateModelController;
use App\ApiResource\Controller\Model\GetModelsByParentIdController;
use App\ApiResource\Controller\Model\GetModelsByUserUuidController;
use App\ApiResource\Controller\Model\GetModelByUserUuidController;
use App\ApiResource\Controller\Model\MovePropsToPublishedAndCacheController;
use App\ApiResource\Controller\Model\RestorePropsFromCacheController;
use App\ApiResource\Controller\Model\SyncDataController;
use App\ApiResource\Controller\Model\UrlResolverController;
use App\ApiResource\Controller\Model\CheckDomainController;
use App\ApiResource\Controller\Model\GetChatBotConfigByModelIdController;
use App\ApiResource\Controller\Model\UpsertModelLogoController;
use App\ApiResource\Dto\Input\Model\ModelSyncInput;
use App\ApiResource\Dto\Input\Model\UrlResolverInput;
use App\ApiResource\Dto\Input\Model\CheckDomainInput;
use App\ApiResource\OpenApi\ModelOpenApiSchema;
use App\ApiResource\Processor\ModelPatchProcessor;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Cocur\Slugify\Slugify;
use App\Validator\Constraints as AppAssert;

#[AppAssert\UniqueUrlPerParent]
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            // security: "is_granted('ROLE_ADMIN')",
            uriTemplate: '/models/{id}/chat_bot_config',
            uriVariables: [
                'id' => new Link(
                    fromClass: null,
                    identifiers: ['id'],
                    parameterName: 'id'
                )
            ],
            read: false,
            controller: GetChatBotConfigByModelIdController::class,
            normalizationContext: ['groups' => ['Model:chatbotConfig:read']],
            openapiContext: [
                'summary' => 'Api to get the chatbot Configuration of the model id given in the parameter',
                // 'security' => [['bearerAuth' => []]],
            ]
        ),
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
            uriTemplate: '/models/resolve-url',
            controller: UrlResolverController::class,
            input: UrlResolverInput::class,
            read: false,
            name: 'models_resolve_url',
            denormalizationContext: ['groups' => ['resolve_url:write']],
            openapiContext: ModelOpenApiSchema::URL_RESOLVER,
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
        new Post(
            uriTemplate: '/models/check-domain',
            controller: CheckDomainController::class,
            input: CheckDomainInput::class,
            read: false,
            write: false,
            name: 'check_model_domain',
            denormalizationContext: ['groups' => ['CheckDomain:write']],
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Verify if the domain is already used by another model',
                'security' => [['bearerAuth' => []]],
            ],
        ),
        new Post(
            uriTemplate: '/models/{id}/logo',
            controller: UpsertModelLogoController::class,
            inputFormats: ['multipart' => ['multipart/form-data']],
            deserialize: false,
            openapi: new Operation(
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                        'description' => 'Logo image file',
                                    ],
                                ],
                                'required' => ['file']
                            ]
                        ]
                    ])
                )
            ),
            read: true, // injects the Model entity as argument
            name: 'post_model_logo',
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Upload a logo for associated model',
                'security' => [['bearerAuth' => []]],
            ],
        ),
        new Patch(
            processor: ModelPatchProcessor::class,
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['Model:write', 'Model:patch:write']],
            openapiContext: [
                'summary' => 'Partially updates only the property specified in the payload',
                'security' => [['bearerAuth' => []]],
            ]
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

    #[ORM\Column(length: 255, nullable: true, unique: false)]
    #[Groups(['Model:read', 'Model:write', 'PageList:read', 'Model:patch:write', 'Model:compact:read'])]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Model:read'])]
    private ?array $propsPublished = null;

    #[ORM\ManyToOne(inversedBy: 'models')]
    #[Groups(['Model:read', 'Model:write', 'PageList:read', 'Model:patch:write', 'Model:compact:read'])]
    private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['PageList:read', 'Model:read', 'Model:write', 'Model:patch:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['PageList:read', 'Model:read', 'Model:write', 'Model:patch:write', 'Model:compact:read'])]
    private ?string $domain = null;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['Model:read', 'Model:patch:write', 'Model:compact:read'])]
    private ?Image $logo = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Model:chatbotConfig:read', 'Model:patch:write'])]
    private ?array $chatBotConfig = null;

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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getLogo(): ?Image
    {
        if ($this->logo) {
            return $this->logo;
        }

        if ($this->parent && $this->parent !== $this) {
            return $this->parent->getLogo();
        }

        return null;
    }

    public function setLogo(?Image $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getChatBotConfig(): ?array
    {
        return $this->chatBotConfig;
    }

    public function setChatBotConfig(?array $chatBotConfig): static
    {
        $this->chatBotConfig = $chatBotConfig;

        return $this;
    }
}
