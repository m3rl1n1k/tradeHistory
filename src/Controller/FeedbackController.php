<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FeedbackController extends AbstractController
{
    public function __construct(protected FeedbackRepository $feedbackRepository)
    {
    }

    #[Route('/feedback', name: 'app_feedback')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedbackList = $this->feedbackRepository->getAll();
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $formData->setDate(new DateTime());
            $formData->setName($this->getUser()->getUserIdentifier());

            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash("success", 'Message was successfully sent!');
            return $this->redirectToRoute('app_feedback');
        }

        return $this->render('feedback/index.html.twig', [
            'feedback_list' => $feedbackList,
            'form' => $form,
        ]);
    }

    #[Route('/close/{id}', name: 'app_close_feedback', methods: ['POST'])]
    public function closeFeedback(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('close' . $feedback->getId(), $request->request->get('_token'))) {
            $feedback->setStatusClose();
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_feedback', [], Response::HTTP_SEE_OTHER);
    }
}
