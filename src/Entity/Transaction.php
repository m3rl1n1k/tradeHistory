<?php


namespace App\Entity;

use App\Enum\TransactionEnum;
use App\Repository\TransactionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;
	
	#[ORM\Column]
	private ?float $amount = null;
	
	#[ORM\Column]
	private ?int $type = null;
	
	#[ORM\Column(type: Types::DATE_MUTABLE)]
	private DateTimeInterface $date;
	
	#[ORM\Column(length: 255, nullable: true)]
	private ?string $description = null;
	
	#[ORM\ManyToOne]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;
	
	#[ORM\ManyToOne(inversedBy: 'transactions')]
	private ?Category $category = null;
	
	
	#[ORM\ManyToOne(inversedBy: 'transactions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Wallet $wallet = null;
	
	public function getId(): ?int
	{
		return $this->id;
	}
	
	public function getWallet(): ?Wallet
	{
		return $this->wallet;
	}
	
	public function setWallet(?Wallet $wallet): static
	{
		$this->wallet = $wallet;
		
		return $this;
	}
	
	public function getAmount(): ?float
	{
		return $this->amount;
	}
	
	public function setAmount(float $amount): static
	{
		$this->amount = $amount;
		
		return $this;
	}
	
	public function getType(): ?int
	{
		return $this->type;
	}
	
	public function setType(int $type): static
	{
		$this->type = $type;
		return $this;
	}
	
	public function getDate(): ?DateTimeInterface
	{
		return $this->date;
	}
	
	public function setDate(DateTimeInterface $date): static
	{
		$this->date = $date;
		
		return $this;
	}
	
	public function getDescription(): ?string
	{
		return $this->description;
	}
	
	public function setDescription(?string $description): static
	{
		$this->description = $description;
		
		return $this;
	}
	
	public function getUserId(): ?string
	{
		return $this->user->getUserIdentifier();
	}
	
	public function setUserId(?User $user): static
	{
		$this->user = $user;
		
		return $this;
	}
	
	public function isIncome(): bool
	{
		return $this->getType() === TransactionEnum::INCOME ?? false;
	}
	
	public function isExpense(): bool
	{
		return $this->getType() === TransactionEnum::EXPENSE ?? false;
	}
	
	public function getCategory(): ?Category
	{
		return $this->category;
	}
	
	public function setCategory(?Category $category): static
	{
		$this->category = $category;
		
		return $this;
	}
	
	public function setId(?int $id): void
	{
		$this->id = $id;
	}
	
	
}
