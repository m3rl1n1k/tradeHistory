<?php

namespace App\Controller;

use App\Entity\Transfer;
use App\Entity\User;
use App\Form\TransferType;
use App\Repository\TransferRepository;
use App\Service\TransferService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
			$transfer->setUser($user);
			$this->transferService->calculate($entityManager, $transfer, $user);
			$entityManager->persist($transfer);
			$entityManager->flush();
			
			return $this->redirectToRoute('app_transfer_index', [], Response::HTTP_SEE_OTHER);
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
	
	#[Route('/{id}', name: 'app_transfer_delete', methods: ['POST'])]
	public function delete(Request $request, Transfer $transfer, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $transfer->getId(), $request->request->get('_token'))) {
			$entityManager->remove($transfer);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('app_transfer_index', [], Response::HTTP_SEE_OTHER);
	}
}
