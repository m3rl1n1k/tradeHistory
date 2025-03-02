<?php

namespace App\Validator;

use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class MainWalletValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof MainWallet) {
            throw new UnexpectedTypeException($constraint, MainWallet::class);
        }

        if (!is_bool($value)) {
            throw new UnexpectedValueException($value, 'bool');
        }

        // Якщо `isMain` == false, валідація не потрібна
        if (!$value) {
            return;
        }

        // Отримуємо поточний об'єкт Wallet
        $wallet = $this->context->getObject();

        if (!$wallet instanceof Wallet) {
            return;
        }

        $user = $wallet->getUser();
        // Перевіряємо, чи вже є основний гаманець у цього користувача
        $repo = $this->entityManager->getRepository(Wallet::class);
        $existingMainWallet = $repo->findOneBy([
            'user' => $user,
            'isMain' => true,
        ]);

        if ($existingMainWallet && $existingMainWallet !== $wallet) {
            $this->context->buildViolation($constraint->message)->atPath('isMain')->addViolation();
        }
    }
}