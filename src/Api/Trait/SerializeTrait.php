<?php

namespace App\Api\Trait;

use App\Entity\User;
use App\Transaction\Entity\Transaction;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait SerializeTrait
{
	public function __construct(protected SerializerInterface $serializer)
	{
	}
	
	public function serializer(mixed  $data, bool $not_circular = false, callable $function = null,
							   string $format = 'json', array $context = [])
	{
		if ($not_circular) {
			return $this->serializer->serialize($data, $format, $context);
		}
		$data = $this->serializer->serialize($data, $format, [
			AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $function
		]);
		return json_decode($data);
	}
	
	public function serializeTransaction(Transaction $transaction, User $user): array
	{
		return [
			'id' => $transaction->getId(),
			'amount' => $transaction->getAmount(),
			'type' => $transaction->getType(),
			'date' => $transaction->getDate()->format('Y-m-d'),
			'description' => $transaction->getDescription(),
			'userId' => $user->getUserIdentifier(),
			'income' => $transaction->isIncome(),
			'expense' => $transaction->isExpense(),
			'category' => [
				'id' => $transaction->getCategory()->getId(),
				'name' => $transaction->getCategory()->getName(),
			],
		];
	}
	
	public function deserializer($data, string $type)
	{
		return $this->serializer->deserialize($data, $type, 'json');
	}
}