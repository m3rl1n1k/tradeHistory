<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
	const LENGTH = 9;
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;
	#[ORM\Column(length: 15, unique: true)]
	private ?string $number = null;
	
	#[ORM\Column(length: 4, nullable: true)]
	private ?string $currency = null;
	
	#[ORM\Column(nullable: true)]
	private ?float $amount = null;
	
	#[ORM\Column(length: 255, nullable: true)]
	private ?string $name = null;
	
	#[ORM\ManyToOne(inversedBy: 'wallets')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;
	
	#[ORM\OneToMany(mappedBy: 'wallet', targetEntity: Transaction::class, orphanRemoval: true)]
	private Collection $transactions;
	
	#[ORM\OneToMany(mappedBy: 'fromWallet', targetEntity: Transfer::class, orphanRemoval: true)]
	private Collection $outcomeTransfer;
	
	#[ORM\OneToMany(mappedBy: 'toWallet', targetEntity: Transfer::class, orphanRemoval: true)]
	private Collection $incomeTransfer;
	
	public function __construct()
	{
		$this->transactions = new ArrayCollection();
		$this->outcomeTransfer = new ArrayCollection();
		$this->incomeTransfer = new ArrayCollection();
	}
	
	public function getId(): ?int
	{
		return $this->id;
	}
	
	public function getNumber(): ?string
	{
		return $this->number;
	}
	
	public function setNumber(string $currency): static
	{
		$number = null;
		for ($i = 1; $i <= self::LENGTH; $i++) {
			$number .= mt_rand(0, 9);
		}
		$this->number = $currency . $number;
		
		return $this;
	}
	
	public function setCustomNumber(string $number): static
	{
		$this->number = $number;
		
		return $this;
	}
	
	public function getCurrency(): ?string
	{
		return $this->currency;
	}
	
	public function setCurrency(?string $currency): static
	{
		$this->currency = $currency;
		
		return $this;
	}
	
	public function getAmount(): ?float
	{
		return $this->amount;
	}
	
	public function setAmount(?float $amount): static
	{
		$this->amount = $amount;
		
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
	
	public function getName(): ?string
	{
		return $this->name;
	}
	
	public function setName(?string $name): void
	{
		$this->name = $name;
	}
	
	/**
	 * @return Collection<int, Transaction>
	 */
	public function getTransactions(): Collection
	{
		return $this->transactions;
	}
	
	public function addTransaction(Transaction $transaction): static
	{
		if (!$this->transactions->contains($transaction)) {
			$this->transactions->add($transaction);
			$transaction->setWallet($this);
		}
		
		return $this;
	}
	
	public function removeTransaction(Transaction $transaction): static
	{
		if ($this->transactions->removeElement($transaction)) {
			// set the owning side to null (unless already changed)
			if ($transaction->getWallet() === $this) {
				$transaction->setWallet(null);
			}
		}
		
		return $this;
	}
	
	public function increment(float $amount): int
	{
		return $this->getAmount() + $amount;
	}
	
	public function decrement(float $amount): int
	{
		return $this->getAmount() - $amount;
	}
	
	/**
	 * @return Collection<int, Transfer>
	 */
	public function getIncomeTransfers(): Collection
	{
		return $this->incomeTransfer;
	}
	
	public function addIncomeTransfer(Transfer $transfer): static
	{
		if (!$this->incomeTransfer->contains($transfer)) {
			$this->incomeTransfer->add($transfer);
			$transfer->setFromWallet($this);
		}
		
		return $this;
	}
	
	public function removeIncomeTransfer(Transfer $transfer): static
	{
		if ($this->incomeTransfer->removeElement($transfer)) {
			// set the owning side to null (unless already changed)
			if ($transfer->getFromWallet() === $this) {
				$transfer->setFromWallet(null);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return Collection<int, Transfer>
	 */
	public function getOutcomeTransfers(): Collection
	{
		return $this->outcomeTransfer;
	}
	
	public function addOutcomeTransfer(Transfer $transfer): static
	{
		if (!$this->outcomeTransfer->contains($transfer)) {
			$this->outcomeTransfer->add($transfer);
			$transfer->setFromWallet($this);
		}
		
		return $this;
	}
	
	public function removeOutcomeTransfer(Transfer $transfer): static
	{
		if ($this->outcomeTransfer->removeElement($transfer)) {
			// set the owning side to null (unless already changed)
			if ($transfer->getFromWallet() === $this) {
				$transfer->setFromWallet(null);
			}
		}
		
		return $this;
	}
}
