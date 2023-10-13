<?php

namespace App\Controller;

use App\Entity\Deposit;
use App\Form\DepositType;
use App\Repository\DepositRepository;
use App\Services\DepositService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/deposit')]
class DepositController extends AbstractController
{
    private Deposit $deposit;

    public function __construct(
        protected DepositService $depositService,
        protected EntityManagerInterface $entityManager
    )
    {
        $this->deposit = new Deposit();
    }

    #[Route('/', name: 'app_deposit_index', methods: ['GET'])]
    public function index(DepositRepository $depositRepository): Response
    {
        return $this->render('deposit/index.html.twig', [
            'deposits' => $depositRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_deposit_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(DepositType::class, $this->deposit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->deposit->setStatus($this->deposit->setOpen());
            $this->entityManager->persist($this->deposit);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_deposit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deposit/new.html.twig', [
            'deposit' => $this->deposit,
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
    public function edit(Request $request, Deposit $deposit): Response
    {
        $form = $this->createForm(DepositType::class, $deposit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_deposit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deposit/edit.html.twig', [
            'deposit' => $deposit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deposit_delete', methods: ['POST'])]
    public function delete(Request $request, Deposit $deposit): Response
    {
        if ($this->isCsrfTokenValid('delete' . $deposit->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($deposit);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_deposit_index', [], Response::HTTP_SEE_OTHER);
    }
}
