<?php

namespace App\Entity;

use App\Repository\TransferRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
class Transfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $walletOut = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $walletIn = null;

    #[ORM\Column(type: 'encrypted_string')]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'transfers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWalletOut(): ?Wallet
    {
        return $this->walletOut;
    }

    public function setWalletOut(?Wallet $walletOut): static
    {
        $this->walletOut = $walletOut;

        return $this;
    }

    public function getWalletIn(): ?Wallet
    {
        return $this->walletIn;
    }

    public function setWalletIn(?Wallet $walletIn): static
    {
        $this->walletIn = $walletIn;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(string|float $amount): static
    {
        if (is_string($amount)) {
            $amount = floatval(str_replace(',', '.', $amount));
        }
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

}