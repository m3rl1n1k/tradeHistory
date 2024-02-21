<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
	const LENGTH = 9;
//	#[ORM\GeneratedValue]
//	#[ORM\Column]
//	private ?int $id = null;
	#[ORM\Id]
	#[ORM\Column(length: 15, unique: true)]
	private ?string $number = null;
	
	#[ORM\Column(length: 4, nullable: true)]
	private ?string $currency = null;
	
	#[ORM\Column(nullable: true)]
	private ?float $amount = null;
	
	#[ORM\ManyToOne(inversedBy: 'wallets')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;
	
	#[ORM\Column]
	private ?bool $isDefault = null;
	
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
		if ($user) {
			$isDefault = $user->getWallets()->filter(fn(Wallet $wallet) => $wallet->isDefault())->first();
			if ($isDefault) {
				if ($isDefault->getNumber() !== $this->getNumber()) {
					throw new InvalidArgumentException('User already has a default wallet');
				}
			}else{
				$this->setIsDefault(true);
				$this->setNumber($this->getCurrency());
			}
		}
		$this->user = $user;
		
		return $this;
	}
	
	public function isDefault(): ?bool
	{
		return $this->isDefault;
	}
	
	public function setIsDefault(bool $isDefault): static
	{
		$this->isDefault = $isDefault;
		
		return $this;
	}
}
