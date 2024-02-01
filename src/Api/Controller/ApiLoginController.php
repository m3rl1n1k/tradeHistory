<?php

namespace App\Api\Controller;

use App\Api\Trait\SerializeTrait;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
	use SerializeTrait;
	
	#[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
	public function index(#[CurrentUser] ?User $user): JsonResponse
	{
		if (is_null($user)) {
			return $this->json([
				'message' => 'missing credentials',
			], Response::HTTP_UNAUTHORIZED);
		}
		$user = $this->serializer($user, function: fn($user)=>$user->getUserId());
		return $this->json(
			[
				'user' => $user,
			]
		);
	}
}
