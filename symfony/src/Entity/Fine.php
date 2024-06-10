<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FineRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Put(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'code' => 'exact',
    'email' => 'exact'
])]
class Fine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[ORM\Column(type: Types::FLOAT)]
    private ?float $value = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'boolean')]
    private ?bool $pay = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(inversedBy: 'fines')]
    private ?User $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function isPay(): ?bool
    {
        return $this->pay;
    }

    public function setPay(bool $pay): static
    {
        $this->pay = $pay;

        return $this;
    }

    public function getEmail(): ?User
    {
        return $this->email;
    }

    public function setEmail(?User $email): static
    {
        $this->email = $email;

        return $this;
    }
}
