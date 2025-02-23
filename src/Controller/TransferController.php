<?php

namespace App\Controller;

use App\Entity\Transfer;
use App\Entity\Wallet;
use App\Form\TransferType;
use App\Repository\TransferRepository;
use App\Service\Interfaces\TransferCalculationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/transfer')]
final class TransferController extends AbstractController
{
    public function __construct(protected TransferCalculationInterface $calculation)
    {
    }

    #[Route(name: 'app_transfer_index', methods: ['GET'])]
    public function index(TransferRepository $transferRepository): Response
    {
        return $this->render('transfer/index.html.twig', [
            'transfers' => $transferRepository->findBy(['user' => $this->getUser()]),
            'pager' => null
        ]);
    }

    #[Route('/new', name: 'app_transfer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transfer = new Transfer();

        $form = $this->createForm(TransferType::class, $transfer, ['wallets' => $this->getUser()->getWallets()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->calculation->calculate('new', $transfer);
            $transfer->setUser($this->getUser());
            $entityManager->persist($transfer);
            $entityManager->flush();

            return $this->redirectToRoute('app_transfer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transfer/new.html.twig', [
            'transfer' => $transfer,
            'form' => $form,
        ]);
    }

//    #[IsGranted('edit', 'transfer')]
//    #[Route('/{id}/edit', name: 'app_transfer_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Transfer $transfer, EntityManagerInterface $entityManager): Response
//    {
//        $transfer = $entityManager->getRepository(Transfer::class)->find($transfer->getId());
//        $oldAmount = $transfer->getAmount();
//        $form = $this->createForm(TransferType::class, $transfer, [
//            'amount' => $transfer->getAmount(),
//        ]);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->calculation->calculate('edit', $transfer, [
//                'oldAmount' => $oldAmount,
//            ]);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_transfer_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('transfer/edit.html.twig', [
//            'transfer' => $transfer,
//            'form' => $form,
//        ]);
//    }
}