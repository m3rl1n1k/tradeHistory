<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    public function __construct(protected TransactionRepository $transactionRepository,
                                protected Security              $security,)
    {
    }

    #[Route('/ajax', name: 'app_ajax')]
    public function index(): Response
    {
        $data = $this->transactionRepository->getAllInString($this->security->getUser()->getId());
        $jsCode = 'var myData = ' . $data . ';';
//        $jsCode = 'var myData = ' . json_encode($data) . ';';

        return new Response($jsCode, Response::HTTP_OK, ['content-type'=>'application/javascript']);
    }
}
