<?php

namespace App\Validator;

use App\Entity\Budget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class BudgetCategoryValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var BudgetCategory $constraint */
        if (!$constraint instanceof BudgetCategory) {
            throw new UnexpectedTypeException($constraint, BudgetCategory::class);
        }

        if (!$value) {
            return;
        }
        $budget = $this->context->getObject();

        $repo = $this->entityManager->getRepository(Budget::class);

        $existingBudget = $repo->findOneBy(['category' => $value, 'month' => $budget->getMonth()]);

        if ($existingBudget && $existingBudget->getMonth() === $budget->getMonth() && $existingBudget->getCategory() === $budget->getCategory()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ category }}', $value->getName())
                ->setParameter('{{ month }}', substr($budget->getMonth(), 0, -3))
                ->addViolation();
        }
    }
}