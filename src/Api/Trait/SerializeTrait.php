<?php

namespace App\Api\Trait;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait SerializeTrait
{
	public function __construct(protected SerializerInterface $serializer)
	{
	}
	
	public function serializer(mixed $data, bool $is_circular = false, callable $function =
	null, string $format = 'json')
	{
		if ($is_circular) {
			$data = $this->serializer->serialize($data, $format);
			return json_decode($data);
		}
		$data = $this->serializer->serialize($data, $format, [
			AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $function,
		]);
		return json_decode($data);
		
		
	}
}