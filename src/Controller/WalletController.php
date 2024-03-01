<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\WalletRepository;
use App\Service\WalletService;
use App\Trait\AccessTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/wallet')]
class WalletController extends AbstractController
{
	public function __construct(
		protected WalletService $walletService
	)
	{
	}
	use AccessTrait;
	#[Route('/', name: 'app_wallet_index')]
	public function index(#[CurrentUser] ?User $user, WalletRepository $walletRepository): Response
	{
		$this->walletService->currencyExchange($user, 'PLN');
		return $this->render('wallet/index.html.twig', [
			'wallets' => $walletRepository->getAll($user),
		]);
	}
	
	#[Route('/new', name: 'app_wallet_new', methods: ['GET', 'POST'])]
	public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): Response
	{
		$wallet = new Wallet();
		$form = $this->createForm(WalletType::class, $wallet);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$wallet->setUser($user);
			$currency = $form->get('currency')->getData();
			$wallet->setNumber($currency);
			$entityManager->persist($wallet);
			$entityManager->flush();
			
			return $this->redirectToRoute('app_wallet_index', [], Response::HTTP_SEE_OTHER);
		}
		
		return $this->render('wallet/new.html.twig', [
			'wallet' => $wallet,
			'form' => $form,
		]);
	}
	
	#[Route('/{number}', name: 'app_wallet_show', methods: ['GET'])]
	public function show(Wallet $wallet): Response
	{
		return $this->render('wallet/show.html.twig', [
			'wallet' => $wallet,
		]);
	}
	
	#[Route('/{number}/edit', name: 'app_wallet_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Wallet $wallet, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(WalletType::class, $wallet);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$formData = $form->getData();
			$currency = $form->get('currency')->getData();
			$number = $this->walletService->editWallet($wallet, $currency);
			$formData->setCustomNumber($number);
			$entityManager->flush();
			
			return $this->redirectToRoute('app_wallet_index', [], Response::HTTP_SEE_OTHER);
		}
		
		return $this->render('wallet/edit.html.twig', [
			'wallet' => $wallet,
			'form' => $form,
		]);
	}
	
	#[Route('/{number}', name: 'app_wallet_delete', methods: ['POST'])]
	public function delete(Request $request, Wallet $wallet, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $wallet->getNumber(), $request->request->get('_token'))) {
			$entityManager->remove($wallet);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('app_wallet_index', [], Response::HTTP_SEE_OTHER);
	}
}
