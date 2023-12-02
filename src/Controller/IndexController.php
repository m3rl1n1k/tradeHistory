<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Service\SerializerService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IndexController extends AbstractController
{
    private UserInterface|User|null $user;

    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected Security              $security,
    )
    {
        $this->user = $this->security->getUser();
    }

    /**
     * @throws NonUniqueResultException
     */
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/home', name: 'app_home')]
    public function home(TransactionRepository $transactionRepository): Response
    {
        $userId = $this->user->getId();
        return $this->render('index/index.html.twig', [
            'last10transaction' => $transactionRepository->findBy(
                [
                    'user' => $userId
                ],
                orderBy: [
                    'id' => 'DESC'
                ],
                limit: 10
            ),
            'income' => $transactionRepository->getSumIncome($userId),
            'expense' => $transactionRepository->getSumExpense($userId)
        ]);
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return !$this->user ? $this->redirectToRoute('app_login') : $this->redirectToRoute('app_home');
    }
}
