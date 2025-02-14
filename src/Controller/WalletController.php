<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\WalletType;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/wallet')]
class WalletController extends AbstractController
{
    public function __construct(
        protected WalletService $walletService
    )
    {
    }

    #[Route('/', name: 'app_wallet_index')]
    public function index(): Response
    {
        return $this->render('wallet/index.html.twig', [
            'wallets' => $this->getUser()->getWallets(),
        ]);
    }

    #[Route('/new', name: 'app_wallet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wallet = new Wallet();
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);

        $isMain = $this->validateIsMainWallet(Wallet::class, $wallet, $entityManager, [
            'data' => $form->getData()
        ]);
        if ($form->isSubmitted() && $form->isValid() && $isMain) {
            $wallet->setUser($this->getUser());
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
        if ($form->isSubmitted() && $form->isValid() && $this->validateIsMainWallet(Wallet::class, $wallet, $entityManager, [
                'data' => $form->getData()
            ])) {
            $currency = $form->get('currency')->getData();
            $wallet->setNumber($currency);
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
