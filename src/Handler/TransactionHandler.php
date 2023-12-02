<?php

namespace App\Handler;

use App\Entity\Transaction;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TransactionHandler
{
    public function __construct(private NormalizerInterface $normalizer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Transaction $transaction, string $format, array $context = []): mixed
    {
        // You can customize the normalization logic here
        $data = $this->normalizer->normalize($transaction, $format, $context);

        // Remove any circular references or sensitive information
        unset($data['password']);

        return $data;
    }
}