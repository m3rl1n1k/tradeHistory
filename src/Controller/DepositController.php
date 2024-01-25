<?php

namespace App\Controller;

use App\Deposit\Entity\Deposit;
use App\Deposit\Repository\DepositRepository;
use App\Form\DepositType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/deposit')]
class DepositController extends AbstractController
{
    #[Route('/', name: 'app_deposit_index', methods: ['GET'])]
    public function index(DepositRepository $depositRepository): Response
    {
        return $this->render('deposit/index.html.twig', [
            'deposits' => $depositRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_deposit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deposit = new Deposit();
        $form = $this->createForm(DepositType::class, $deposit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deposit);
            $entityManager->flush();

            return $this->redirectToRoute('app_deposit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deposit/new.html.twig', [
            'deposit' => $deposit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deposit_show', methods: ['GET'])]
    public function show(Deposit $deposit): Response
    {
        return $this->render('deposit/show.html.twig', [
            'deposit' => $deposit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deposit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deposit $deposit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepositType::class, $deposit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_deposit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deposit/edit.html.twig', [
            'deposit' => $deposit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deposit_delete', methods: ['POST'])]
    public function delete(Request $request, Deposit $deposit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deposit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($deposit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_deposit_index', [], Response::HTTP_SEE_OTHER);
    }
}
