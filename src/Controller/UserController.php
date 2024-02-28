<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
	public function __construct(
		protected UserRepository $userRepository)
	{
	}
	
	#[Route('/{id}', name: 'app_user_index', methods: ['GET', 'POST'])]
	public function index(int $id, Request $request, User $user, EntityManagerInterface $entityManager):Response
	{
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->flush();
			
			return $this->redirectToRoute('app_user_index', ['id'=>$id], Response::HTTP_SEE_OTHER);
		}
		
		return $this->render('user/index.html.twig', [
			'user' => $user,
			'form' => $form,
		]);
	}
}
