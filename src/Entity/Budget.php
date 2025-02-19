<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?float $planingAmount = null;

    #[ORM\Column]
    private ?float $actualAmount = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getPlaningAmount(): ?float
    {
        return $this->planingAmount;
    }

    public function setPlaningAmount(float $planingAmount): static
    {
        $this->planingAmount = $planingAmount;

        return $this;
    }

    public function getActualAmount(): ?float
    {
        return $this->actualAmount;
    }

    public function setActualAmount(float $actualAmount): static
    {
        $this->actualAmount = $actualAmount;

        return $this;
    }
}
