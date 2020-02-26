<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $response = [
            'code' => 200,
            'response' => $this->render('dashboard.html.twig')->getContent()
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/messagerie", name="mailbox")
     */
    public function mailbox()
    {
        return $this->render('mailbox/index.html.twig');
        /*$response = [
            'code' => 200,
            'response' => $this->render('mailbox/index.html.twig')->getContent()
        ];
        return new JsonResponse($response);*/
    }

}
