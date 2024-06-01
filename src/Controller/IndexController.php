<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Service\ChartService;
use App\Service\WalletService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IndexController extends AbstractController
{
    public function __construct(
        protected ChartService       $chartService,
        protected CategoryRepository $CategoryRepository,
        protected WalletService      $walletService,
    )
    {
    }

    #[Route('/', name: 'app_index')]
    public function index(): RedirectResponse
    {
        if ($this->getUser() === null) {
            $uri = $this->redirectToRoute('app_login');
        } else {
            $uri = $this->redirectToRoute('app_home');
        }
        return $uri;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function home(TransactionRepository $transactionRepository):
    Response
    {
        return $this->render('index/index.html.twig', [
            'last10transaction' => $transactionRepository->getUserTransactions(['date' => 'DESC'], 10),
            'chart' => $this->chartService->dashboardChart(),
            'amount' => $this->walletService->getTotal()
        ]);
    }


}
