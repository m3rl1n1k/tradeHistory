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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
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
            $date = new DateTime();
            $formData->setDate($date);
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

    #[Route('/delete/{id}', name: 'app_delete_feedback', methods: ['POST'])]
    public function delete(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $feedback->getId(), $request->request->get('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_feedback', [], Response::HTTP_SEE_OTHER);
    }
}
