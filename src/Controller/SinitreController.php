<?php


namespace App\Controller;


use App\Service\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sinistres")
 */
class SinitreController extends AbstractController
{

    /**
     * @Route("/", name="sinistres" ,methods={"GET"})
     */
    public function index(Api $api)
    {
        $sinistres = $api->get(getenv('TOKEN_LIST_DEC_SINISTRE'))->result;
        return $this->render('sinistre/liste.html.twig',['sinistres'=>$sinistres]);
        /*
        $response = array(
            "code" => 200,
            "response" => $this->render('sinistre/liste.html.twig',['sinistres'=>$sinistres])->getContent());
        return new JsonResponse($response);*/
    }

    /**
     * @Route("/show/{id}", name="show-dec-sinistre" ,methods={"GET"})
     */
    public function showSinitre(Api $api,$id)
    {
        $sinistre = $api->get([
            'token' => getenv('TOKEN_SHOW_DEC_SINISTRE'),
            'id' => $id
        ])->result;
        $response = array(
            "code" => 200,
            "response" => $this->render('sinistre/show.html.twig',['sinistre'=>$sinistre[0],])->getContent());
        return new JsonResponse($response);
    }

}