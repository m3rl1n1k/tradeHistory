<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, TransactionRepository $transactionRepository, CategoryRepository $categoryRepository): Response
    {
        $list = [];
        $category = $categoryRepository->findOneBy(['name' => ucfirst(htmlspecialchars($request->query->get('s')))]);
        if ($category !== null) {
            $list = $transactionRepository->searchByCategory($category->getId());
        }

        return $this->render('search/index.html.twig', [
            'list' => $list,
            'count' => count($list),
        ]);
    }
}