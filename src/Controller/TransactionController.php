<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\ParentCategoryRepository;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use App\Service\Pagination\PaginateInterface;
use App\Service\Transaction\CalculationInterface;
use App\Trait\TransactionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        protected CalculationInterface     $calculation,
        protected ParentCategoryRepository $parentCategoryRepository,
        protected WalletRepository         $walletRepository,)
    {
    }

    use TransactionTrait;

    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(Request $request, TransactionRepository $transactionRepository, PaginateInterface $paginate): Response
    {
        $query = $transactionRepository->getUserTransactions(true);
        return $this->render('transaction/index.html.twig', [
            'pagerfanta' => $paginate->paginate($query, $request),
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
        $form = $this->getForm($transaction);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData()->setUser($this->getUser());
            $this->calculation->calculate('new', $transaction);
            $entityManager->persist($transaction);
            $entityManager->flush();
            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    #[IsGranted('view', 'transaction')]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'transaction')]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $oldAmount = $transaction->getAmount();
        $form = $this->getForm($transaction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->calculation->calculate('edit', $transaction, options: ['oldAmount' => $oldAmount]);

            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $this->calculation->calculate('remove', $transaction);
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
