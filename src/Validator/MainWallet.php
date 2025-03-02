<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute()]
final class MainWallet extends Constraint
{
    public string $message = 'You can have only one wallet as main';

    // You can use #[HasNamedArguments] to make some constraint options required.
    // All configurable options must be passed to the constructor.
    public function __construct(
        public string $mode = 'strict',
        ?array        $groups = null,
        mixed         $payload = null
    )
    {
        parent::__construct([], $groups, $payload);
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;

    }
}