<?php

namespace App\Entity;

use App\Repository\TransferRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
class Transfer
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;
	
	#[ORM\ManyToOne(inversedBy: 'transfers')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;
	
	#[ORM\ManyToOne(inversedBy: 'outcomeTransfer')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Wallet $fromWallet = null;
	
	#[ORM\ManyToOne(inversedBy: 'incomeTransfer')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Wallet $toWallet = null;
	
	#[ORM\Column]
	private ?float $amount = null;
	
	#[ORM\Column(type: Types::DATE_MUTABLE)]
	private ?DateTimeInterface $date = null;
	
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
	
	public function getFromWallet(): ?Wallet
	{
		return $this->fromWallet;
	}
	
	public function setFromWallet(?Wallet $fromWallet): static
	{
		$this->fromWallet = $fromWallet;
		
		return $this;
	}
	
	public function getToWallet(): ?Wallet
	{
		return $this->toWallet;
	}
	
	public function setToWallet(?Wallet $toWallet): static
	{
		$this->toWallet = $toWallet;
		
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
	
	public function getDate(): ?DateTimeInterface
	{
		return $this->date;
	}
	
	public function setDate(): static
	{
		$this->date = new DateTime('now');
		
		return $this;
	}
    public function getUserId(): string
    {
        return $this->getUser()->getId();
    }
}
