<?php

namespace App\Service;

use App\Handler\TransactionHandler;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerService
{

    public function __construct(protected SerializerInterface $serializer){}
    public function serializeArray(array $transactions): string
    {
        return $this->serializer->serialize($transactions, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['userId', 'id', 'description', 'category'],
            'normalizer' => new TransactionHandler($this->serializer),
        ]);
    }
}