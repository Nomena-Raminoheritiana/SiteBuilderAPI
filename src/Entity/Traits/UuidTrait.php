<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

trait UuidTrait
{
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    protected ?int $id = null;


    #[Groups(['User:read'])]
    #[ORM\Column(name: 'uuid', type: 'uuid', length: 36, unique: true)]
    protected Uuid $uuid;

    public function __construct()
    {
        $this->generateUuid();
    }

     public function initializeUuid(): void
    {
        $this->generateUuid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid|string $uuid): self
    {
        if (is_string($uuid)) {
            $this->uuid = Uuid::fromString($uuid);
        } elseif ($uuid instanceof Uuid) {
            $this->uuid = $uuid;
        } else {
            throw new \InvalidArgumentException('$uuid is invalid.');
        }

        return $this;
    }

    private function generateUuid(): self
    {
        $this->uuid = Uuid::v4();

        return $this;
    }
}
