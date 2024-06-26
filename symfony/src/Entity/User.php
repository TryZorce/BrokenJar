<?php

namespace App\Entity;

use App\Repository\UserRepository;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\SignUpController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
#[ApiResource(
    operations: [
        new Post(
            controller: SignUpController::class
        ),
        new Get()
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\Email(message: "L'email n'est pas valide.")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: "Le numéro de carte est requis.")]
    #[Assert\Regex(
        pattern: "/^[0-9]{16}$/",
        message: "Le numéro de carte doit contenir exactement 16 chiffres."
    )]
    private ?string $card = null;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank(message: "Le CVV est requis.")]
    #[Assert\Regex(
        pattern: "/^[0-9]{3}$/",
        message: "Le CVV doit être un entier composé de trois chiffres."
    )]
    private ?string $crypto = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: "La date d'expiration est requise.")]
    #[Assert\Regex(
        pattern: "/^(0[1-9]|1[0-2])\/?([0-9]{2})$/",
        message: "Le format de la date d'expiration doit être MM/YY."
    )]
    private ?string $expiry = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est requis.")]
    #[Assert\Regex(
        pattern: "/^0[1-9]([-. ]?[0-9]{2}){4}$/",
        message: "Le numéro de téléphone n'est pas valide."
    )]
    private ?string $phone = null;

    #[Assert\Length(min: 3, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^[A-Za-zÀ-ÖØ-öø-ÿ '-]+$/",
        message: "Les caractères saisis ne sont pas corrects."
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\Length(min: 3, minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^[A-Za-zÀ-ÖØ-öø-ÿ '-]+$/",
        message: "Les caractères saisis ne sont pas corrects."
    )]
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[Assert\Length(min: 5, minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.")]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    /**
     * @var Collection<int, Fine>
     */
    #[ORM\OneToMany(targetEntity: Fine::class, mappedBy: 'email')]
    private Collection $fines;

    public function __construct()
    {
        $this->fines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCard(): ?string
    {
        return $this->card;
    }

    public function setCard(string $card): static
    {
        $this->card = $card;

        return $this;
    }

    public function getCrypto(): ?string
    {
        return $this->crypto;
    }

    public function setCrypto(string $crypto): static
    {
        $this->crypto = $crypto;

        return $this;
    }

    public function getExpiry(): ?string
    {
        return $this->expiry;
    }

    public function setExpiry(string $expiry): static
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * @return Collection<int, Fine>
     */
    public function getFines(): Collection
    {
        return $this->fines;
    }

    public function addFine(Fine $fine): static
    {
        if (!$this->fines->contains($fine)) {
            $this->fines->add($fine);
            $fine->setEmail($this);
        }

        return $this;
    }

    public function removeFine(Fine $fine): static
    {
        if ($this->fines->removeElement($fine)) {
            if ($fine->getEmail() === $this) {
                $fine->setEmail(null);
            }
        }

        return $this;
    }
}
