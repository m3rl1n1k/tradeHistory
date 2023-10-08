<?php

namespace App\Entity;

use App\Enum\TransactionTypesEnum;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
	#[ORM\Id]
         	#[ORM\GeneratedValue]
         	#[ORM\Column]
         	private ?int $id = null;

	#[ORM\ManyToOne]
         	#[ORM\JoinColumn(nullable: false)]
         	private ?User $user = null;

	#[ORM\Column]
         	private ?float $amount = null;

	#[ORM\Column(length: 20)]
         	private ?string $type = null;

	#[ORM\Column(type: Types::DATE_MUTABLE)]
         	private ?\DateTimeInterface $date = null;

	#[ORM\ManyToOne(targetEntity: Category::class, cascade: ['remove'])]
         	#[ORM\JoinColumn(name: 'category', referencedColumnName: 'id', onDelete: 'CASCADE')]
         	private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

	public function getId(): ?int
         	{
         		return $this->id;
         	}

	public function setUserId(?User $user): static
         	{
         		$this->user = $user;
         
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


	public function getType(): ?string
         	{
         		return $this->type;
         	}

	public function setType(string $type): static
         	{
         		if ($type ===  TransactionTypesEnum::Income) {
         			$this->setIncome();
         		} elseif ($type === TransactionTypesEnum::Expense) {
         			$this->setExpense();
         		} else {
         			throw new \LogicException('Not correct type is set!');
         		}
         		return $this;
         	}

	protected function setIncome(): static
         	{
         		$this->type = TransactionTypesEnum::Income;
         		return $this;
         	}

	protected function setExpense(): static
         	{
         		$this->type = TransactionTypesEnum::Expense;
         		return $this;
         	}

	public function getDate(): ?\DateTimeInterface
         	{
         		return $this->date;
         	}

	public function setDate(\DateTimeInterface $date): static
         	{
         		$this->date = $date;
         
         		return $this;
         	}

	public function getCategoryId(): ?Category
         	{
         		return $this->category;
         	}

	public function setCategoryId(?Category $category): static
         	{
         		$this->category = $category;
         
         		return $this;
         	}

	public function sUserId(): User
         	{
         		return $this->getUserId();
         	}

	public function getUserId(): ?User
         	{
         		return $this->user;
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
}
