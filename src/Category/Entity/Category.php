<?php

namespace App\Category\Entity;

use App\Category\Repository\CategoryRepository;
use App\Entity\User;
use App\Transaction\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
	
	public function __construct()
	{
		$this->transactions = new ArrayCollection();
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
}
