<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\TransactionType;
use App\Repository\ParentCategoryRepository;
use App\Repository\WalletRepository;
use App\Service\SettingService;
use App\Trait\AccessTrait;
use App\Transaction\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Exception;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        protected TransactionService       $transactionService,
        protected ParentCategoryRepository $parentCategoryRepository,
        protected WalletRepository         $walletRepository,
        protected SettingService           $settingService)
    {
    }

    use  AccessTrait;

    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $query = $this->transactionService->getTransactions();
        $pagerfanta = $this->paginate($query, $request);
        return $this->render('transaction/index.html.twig', [
            'pagerfanta' => $pagerfanta
        ]);
    }

    public function paginate(Query $query, Request $request): Pagerfanta
    {
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);

        $currentPage = $request->query->getInt('page', 1);
        $pagerfanta->setMaxPerPage(SettingService::getTransactionsPerPage());
        $pagerfanta->setCurrentPage($currentPage);

        return $pagerfanta;
    }

    /**
     * @throws Exception
     */
    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction, [
            'category' => $this->parentCategoryRepository->getMainAndSubCategories(),
            'wallet' => $this->walletRepository->getAll(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var Transaction $formData */
            $formData = $form->getData();
            $formData->setUser($user);

            $id = $form->get('wallet')->getData();
            $wallet = $this->walletRepository->find($id);
            $this->transactionService->newTransaction($wallet, $transaction);
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
    public function show(Transaction $transaction): Response
    {
        $this->accessDenied($transaction->getUser()->getId(), $this->getUser());
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $this->accessDenied($transaction->getUser()->getId(), $this->getUser());

        $oldAmount = $transaction->getAmount();
        $form = $this->createForm(TransactionType::class, $transaction, [
            'category' => $this->parentCategoryRepository->getMainAndSubCategories(),
            'wallet' => $this->walletRepository->getAll()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $wallet = $form->get('wallet')->getData();
            $this->transactionService->editTransaction($wallet, $transaction, $oldAmount);

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
        $this->accessDenied($transaction->getUser()->getId(), $this->getUser());

        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $this->transactionService->removeTransaction($transaction->getWallet(), $transaction);
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
