<?php

namespace App\Controller;

use App\Security\Tools;
use App\Service\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/demandes/retractation")
 */
class DemandeRetractationContoller extends AbstractController
{
    /**
     * @Route("/", name="retractations")
     */
    public function index(Api $api)
    {
        $demandes = $api->get(getenv('TOKEN_LIST_DEMANDE_RETRAC'))->result;
        return $this->render('demande/retractation/index.html.twig', ["demandes" => $demandes]);
        /*
        $response = [
            'code' => 200,
            'response' => $this->render('demande/retractation/index.html.twig', ["demandes" => $demandes])->getContent()
        ];
        return new JsonResponse($response);*/

    }


    /**
     * @Route("/valid/{id}", name="valid-demande-retract")
     */
    public function valideDemande(Request $request, $id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_VALID_DEMANDE_RETRAC'),
            'id' => $id
        ]);
        if($output->status == 200) {
            $this->addFlash('success', 'La demande a bien été validé ');
        } else {
            $this->addFlash('error', 'Un problème est survenu...');
        }
        return $this->redirectToRoute('retractations');
    }

    /**
     * @Route("/decline/{id}", name="decline-demande-retract")
     */
    public function declineDemande(Request $request, $id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_DECLINE_DEMANDE_RETRAC'),
            'id' => $id
        ]);
        if ($output->status == 200)
            $this->addFlash('success', 'La demande a bien été enregistrée ');
        else
            $this->addFlash('error', 'Un problème est survenu...');
        return $this->redirectToRoute('retractations');
    }
}