<?php

namespace App\Controller;

use App\Helper\StringHelper;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class SearchController extends AbstractController
{

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, TransactionRepository $transactionRepository, CategoryRepository $categoryRepository): Response
    {
        $list = [];
        $category = $categoryRepository->findOneBy(['name' => StringHelper::uc_first(htmlspecialchars($request->query->get('s')))]);
        if ($category !== null) {
            $list = $transactionRepository->searchByCategory($category->getId());
        }

        return $this->render('search/index.html.twig', [
            'list' => $list,
        ]);
    }
}