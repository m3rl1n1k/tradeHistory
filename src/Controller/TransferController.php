<?php

namespace App\Controller;

use App\Entity\Transfer;
use App\Entity\User;
use App\Form\TransferType;
use App\Repository\TransferRepository;
use App\Service\TransferService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/transfer')]
class TransferController extends AbstractController
{
	public function __construct(protected TransferService $transferService)
	{
	}
	
	#[Route('/', name: 'app_transfer_index', methods: ['GET'])]
	public function index(#[CurrentUser] ?User $user, TransferRepository $transferRepository): Response
	{
		return $this->render('transfer/index.html.twig', [
			'transfers' => $transferRepository->getAll($user),
		]);
	}
	
	#[Route('/new', name: 'app_transfer_new', methods: ['GET', 'POST'])]
	public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): Response
	{
		$transfer = new Transfer();
		$form = $this->createForm(TransferType::class, $transfer, [
			'user' => $user
		]);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$entityManager->beginTransaction();
				$transfer->setUser($user);
				$transfer->setDate();
				$this->transferService->calculate($entityManager, $transfer, $user);
				$entityManager->persist($transfer);
				$entityManager->flush();
				$entityManager->commit();
				
				return $this->redirectToRoute('app_transfer_index', [], Response::HTTP_SEE_OTHER);
			} catch (Exception $exception) {
				$entityManager->rollback();
			}
		}
		
		return $this->render('transfer/new.html.twig', [
			'transfer' => $transfer,
			'form' => $form,
		]);
	}
	
	#[Route('/{id}', name: 'app_transfer_show', methods: ['GET'])]
	public function show(Transfer $transfer): Response
	{
		return $this->render('transfer/show.html.twig', [
			'transfer' => $transfer,
		]);
	}
}
