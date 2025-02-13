<?php

namespace App\Entity;

use App\Helper\StringHelper;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_CATEGORY_NAME', fields: ['name'])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: ParentCategory::class, inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ParentCategory $parentCategory = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $color = null;

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
        $this->name = StringHelper::uc_first($name);
        return $this;
    }

    public function getParentCategory(): ?ParentCategory
    {
        return $this->parentCategory;
    }

    public function setParentCategory(?ParentCategory $parentCategory): static
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
