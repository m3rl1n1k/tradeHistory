<?php

namespace App\Api\Controller;

use App\Api\Entity\ApiToken;
use App\Api\Repository\ApiTokenRepository;
use App\Api\Service\GenerateApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/api/token')]
class ApiTokenController extends AbstractController
{
	#[Route('/', name: 'app_api_token_index', methods: ['GET'])]
	public function index(ApiTokenRepository $apiTokenRepository): Response
	{
		return $this->render('api_token/index.html.twig', [
			'api_tokens' => $apiTokenRepository->findAll(),
		]);
	}
	
	#[Route('/generate', name: 'app_api_token_new', methods: ['GET', 'POST'])]
	public function new(EntityManagerInterface $entityManager, TokenInterface $token): Response
	{
		$apiToken = new ApiToken();
		$apiToken->setToken(GenerateApiToken::generateApiToken());
		$apiToken->setUser($token->getUser());
		$entityManager->persist($apiToken);
		$entityManager->flush();
		
		
		return $this->redirectToRoute('app_api_token_index', [], Response::HTTP_SEE_OTHER);
	}
	
	#[Route('/{id}', name: 'app_api_token_delete', methods: ['POST'])]
	public function delete(Request $request, ApiToken $apiToken, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $apiToken->getId(), $request->request->get('_token'))) {
			$entityManager->remove($apiToken);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('app_api_token_index', [], Response::HTTP_SEE_OTHER);
	}
}
