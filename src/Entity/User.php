<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\UuidTrait;
use App\Repository\UserRepository;
use App\ApiResource\Processor\UserRegistrationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['email'], message: 'Email already exists!')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[
   ApiResource(
        operations: [
            new Post(
                processor: UserRegistrationProcessor::class,
            )
        ],
        outputFormats: ['json' => ['application/json']],
        denormalizationContext: ['groups' => ['User:write']],
        normalizationContext: ['groups' => ['User:read']]
    ) 
]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use UuidTrait;

    #[Groups(['User:write', 'User:read'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Groups(['User:write'])]
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['User:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[Groups(['User:write', 'User:read'])]
    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[Groups(['User:write', 'User:read'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $businessActivity = null;

    #[Groups(['User:write', 'User:read'])]
    #[ORM\Column(length: 255)]
    private ?string $businessLocation = null;

    /**
     * @var Collection<int, Model>
     */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $modeles;

    public function __construct()
    {
        $this->modeles = new ArrayCollection();
        $this->initializeUuid();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getBusinessActivity(): ?string
    {
        return $this->businessActivity;
    }

    public function setBusinessActivity(string $businessActivity): static
    {
        $this->businessActivity = $businessActivity;

        return $this;
    }

    public function getBusinessLocation(): ?string
    {
        return $this->businessLocation;
    }

    public function setBusinessLocation(string $businessLocation): static
    {
        $this->businessLocation = $businessLocation;

        return $this;
    }

    /**
     * @return Collection<int, Model>
     */
    public function getModeles(): Collection
    {
        return $this->modeles;
    }

    public function addModele(Model $modele): static
    {
        if (!$this->modeles->contains($modele)) {
            $this->modeles->add($modele);
            $modele->setUser($this);
        }

        return $this;
    }

    public function removeModele(Model $modele): static
    {
        if ($this->modeles->removeElement($modele)) {
            // set the owning side to null (unless already changed)
            if ($modele->getUser() === $this) {
                $modele->setUser(null);
            }
        }

        return $this;
    }
}
