<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Service\ChartService;
use App\Service\WalletService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    public function __construct(
        protected ChartService       $chartService,
        protected CategoryRepository $CategoryRepository,
        protected WalletService      $walletService,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function home(TransactionRepository $transactionRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'last_transaction' => $transactionRepository->getLastTransaction(),
            'chart' => $this->chartService->dashboardChart(),
            'amount' => $this->walletService->getTotal()
        ]);
    }
}
