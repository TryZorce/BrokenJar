<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;

#[ORM\Entity(repositoryClass: FineRepository::class)]
#[ApiResource]

#[ApiFilter(SearchFilter::class, properties: ['code' => 'exact'])]
class Fine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\Column]
    private ?bool $pay = null;

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
