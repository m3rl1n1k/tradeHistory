<?php

namespace App\Entity;

use App\Repository\UserSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingRepository::class)]
class UserSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $coloredCategories;

    #[ORM\Column(nullable: true)]
    private ?bool $coloredParentCategories;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $colorExpenseChart;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $colorIncomeChart;

    #[ORM\Column(nullable: true)]
    private ?int $transactionsPerPage;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $defaultColorForCategoryAndParent;

    #[ORM\Column(nullable: true)]
    private ?bool $showColorInTransactionList;

    public function __construct()
    {
        $this->coloredCategories = false;
        $this->coloredParentCategories = false;
        $this->colorExpenseChart = '#3r3r3r';
        $this->colorIncomeChart = '#eeeeee';
        $this->transactionsPerPage = 20;
        $this->defaultColorForCategoryAndParent = '#aaaaaa';
        $this->showColorInTransactionList = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isColoredCategories(): ?bool
    {
        return $this->coloredCategories;
    }

    public function setColoredCategories(?bool $coloredCategories): static
    {
        $this->coloredCategories = $coloredCategories;

        return $this;
    }

    public function isColoredParentCategories(): ?bool
    {
        return $this->coloredParentCategories;
    }

    public function setColoredParentCategories(?bool $coloredParentCategories): static
    {
        $this->coloredParentCategories = $coloredParentCategories;
        return $this;
    }

    public function getColorExpenseChart(): ?string
    {
        return $this->colorExpenseChart;
    }

    public function setColorExpenseChart(?string $colorExpenseChart): static
    {
        $this->colorExpenseChart = $colorExpenseChart;
        return $this;
    }

    public function getColorIncomeChart(): ?string
    {
        return $this->colorIncomeChart;
    }

    public function setColorIncomeChart(?string $colorIncomeChart): static
    {
        $this->colorIncomeChart = $colorIncomeChart;
        return $this;
    }

    public function getTransactionsPerPage(): ?int
    {
        return $this->transactionsPerPage;
    }

    public function setTransactionsPerPage(?int $transactionsPerPage): static
    {
        $this->transactionsPerPage = $transactionsPerPage;

        return $this;
    }

    public function getDefaultColorForCategoryAndParent(): ?string
    {
        return $this->defaultColorForCategoryAndParent;
    }

    public function setDefaultColorForCategoryAndParent(?string $defaultColorForCategoryAndParent): static
    {
        $this->defaultColorForCategoryAndParent = $defaultColorForCategoryAndParent;

        return $this;
    }

    public function isShowColorInTransactionList(): ?bool
    {
        return $this->showColorInTransactionList;
    }

    public function setShowColorInTransactionList(?bool $showColorInTransactionList): self
    {
        $this->showColorInTransactionList = $showColorInTransactionList;
        return $this;
    }
}
