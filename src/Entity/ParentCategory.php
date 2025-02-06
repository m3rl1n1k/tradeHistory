<?php

namespace App\Entity;

use App\Repository\ParentCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParentCategoryRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PARENT_NAME', fields: ['name'])]
class ParentCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'parentCategory', targetEntity: Category::class, orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $categories;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $color = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
        $this->name = ucfirst($name);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $Category): static
    {
        if (!$this->categories->contains($Category)) {
            $this->categories->add($Category);
            $Category->setParentCategory($this);
        }

        return $this;
    }

    public function removeCategory(Category $Category): static
    {
        if ($this->categories->removeElement($Category)) {
            // set the owning side to null (unless already changed)
            if ($Category->getParentCategory() === $this) {
                $Category->setParentCategory(null);
            }
        }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
