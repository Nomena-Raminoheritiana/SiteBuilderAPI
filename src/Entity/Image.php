<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\ApiResource\Controller\Image\ImageDownloadController;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Uploadable]
#[ApiResource(
    types: ['https://schema.org/Image'],
    operations: [
        new Get(),
        new Get(
            name: "api_image_download",
            uriTemplate: "/image-download/file/{id}",
            uriVariables: [
                'id' => new Link(
                    fromClass: Image::class,
                    identifiers: ['id']
                )
            ],
            controller: ImageDownloadController::class,
            read: true,
            output: false
        ),
        new Delete(
            uriTemplate: "/images/{idFromFront}/{modelId}",
            uriVariables: [
                'idFromFront' => 'idFromFront',
                'modelId' => new Link(
                    fromProperty: "images",
                    fromClass: Model::class
                )
            ],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            types: ['https://schema.org/Image'],
            inputFormats: ['multipart' => ['multipart/form-data']],
            openapi: new Operation(
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            ),
            security: "is_granted('ROLE_ADMIN')",
        )
    ],
    outputFormats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['Image:read']]
)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Image:read', 'Image:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Image:read'])]
    #[ApiProperty(writable: false)]
    private ?string $name = null;

   #[UploadableField(mapping: 'image', fileNameProperty: 'name')]
    private ?File $file = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Image:read'])]
    private ?string $idFromFront = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ApiProperty(readable: true)]
    #[Groups(['Image:read'])]
    private ?Model $model = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

    #[ApiProperty(writable: false, types: ['https://schema.org/url'])]
    #[Groups(['Image:read'])]
    private ?string $url = null;

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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;
        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getIdFromFront(): ?string
    {
        return $this->idFromFront;
    }

    public function setIdFromFront(string $idFromFront): static
    {
        $this->idFromFront = $idFromFront;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
