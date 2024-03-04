<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;
	
	#[ORM\Column(length: 255)]
	private ?string $name = null;
	
	#[ORM\OneToMany(mappedBy: 'category', targetEntity: Transaction::class)]
	#[ORM\JoinColumn(onDelete: 'cascade')]
	private Collection $transactions;
	
	#[ORM\ManyToOne]
	private ?User $user = null;
	
	#[ORM\OneToMany(mappedBy: 'category', targetEntity: SubCategory::class, orphanRemoval: true)]
	private Collection $subCategories;
	
	public function __construct()
	{
		$this->transactions = new ArrayCollection();
		$this->subCategories = new ArrayCollection();
	}
	
	public function getId(): ?int
	{
		return $this->id;
	}
	
	public function getUser(): ?User
	{
		return $this->user;
	}
	
	public function getUserId(): string
	{
		return $this->user->getUserIdentifier();
	}
	
	public function setUser(?User $user): static
	{
		$this->user = $user;
		
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
	
	/**
	 * @return Collection<int, Transaction>
	 */
	public function getTransactions(): Collection
	{
		return $this->transactions;
	}
	
	public function setId(?int $id): void
	{
		$this->id = $id;
	}
	
	/**
	 * @return Collection<int, SubCategory>
	 */
	public function getSubCategories(): Collection
	{
		return $this->subCategories;
	}
	
	public function addSubCategory(SubCategory $subCategory): static
	{
		if (!$this->subCategories->contains($subCategory)) {
			$this->subCategories->add($subCategory);
			$subCategory->setCategory($this);
		}
		
		return $this;
	}
	
	public function removeSubCategory(SubCategory $subCategory): static
	{
		if ($this->subCategories->removeElement($subCategory)) {
			// set the owning side to null (unless already changed)
			if ($subCategory->getCategory() === $this) {
				$subCategory->setCategory(null);
			}
		}
		
		return $this;
	}
}
