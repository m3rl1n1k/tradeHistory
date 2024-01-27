<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityApiController extends AbstractController
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    #[Route(path: '/api/login', name: 'app_api_login', methods: ['GET','POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, #[CurrentUser] ?User $user): Response
    {
        if ($this->getUser()) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $token = $this->tokenStorage->getToken()->getUser();
        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $request->headers->get('Authorization'),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}