<?php

namespace App\Controller;

use App\Repository\SubCategoryRepository;
use App\Repository\TransactionRepository;
use App\Service\ChartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class IndexController extends AbstractController
{
    public function __construct(
        protected ChartService          $chartService,
        protected SubCategoryRepository $subCategoryRepository
    )
    {
    }

    #[Route('/', name: 'app_index')]
    public function index(Request $request): RedirectResponse
    {
        define("App\Controller\LOCALE", $request->getLocale());
        if (!$this->getUser()) {
            $uri = $this->redirectToRoute('app_login');
        } else {
            $uri = $this->redirectToRoute('app_home');
        }
        return $uri;
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function home(TransactionRepository $transactionRepository, Request $request):
    Response
    {
        $categoryList = $request->query->keys();
        $categoryList = array_map(function ($value){
            return str_replace('_', ' ', $value);
        },$categoryList);

        return $this->render('index/index.html.twig', [
            'categories_list' => $categoryList,
            'categories' => $this->chartService->getCategoriesList(),
            'last10transaction' => $transactionRepository->getUserTransactions(['date' => 'DESC'], 20),
            'chart' => $this->chartService->dashboardChart($categoryList)
        ]);
    }


}
