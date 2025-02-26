<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use App\Service\CryptService;
use App\Service\Interfaces\CrypticInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LogicException;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{

    const LENGTH = 9;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;
    #[ORM\Column(length: 15, unique: true)]
    private ?string $number = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(type: 'encrypted_string', nullable: false)]
    private ?string $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $isMain = null;

    #[ORM\ManyToOne(inversedBy: 'wallets')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: Transaction::class, orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $currency): static
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

    public function increment(?float $amount): float
    {
        $this->amountNotNull($amount);
        return $this->getAmount() + $amount;
    }

    protected function amountNotNull($amount): void
    {
        if ($amount === null) {
            throw new LogicException('You forget enter amount!?');
        }
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

    public function decrement(?float $amount): float
    {
        $this->amountNotNull($amount);
        return $this->getAmount() - $amount;
    }

    public function isMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(?bool $isMain): Wallet
    {
        $this->isMain = $isMain;
        return $this;
    }
}