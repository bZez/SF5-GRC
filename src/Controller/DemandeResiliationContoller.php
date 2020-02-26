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
 * @Route("/demandes/resiliations")
 */
class DemandeResiliationContoller extends AbstractController
{
    /**
     * @Route("/", name="resiliations")
     */
    public function index(Api $api)
    {
        $demandes = $api->get(getenv('TOKEN_LIST_DEMANDE_RESIL'))->result;
        return $this->render('demande/resiliation/index.html.twig', ["demandes" => $demandes]);
            /*
        $response = [
            'code' => 200,
            'response' => $this->render('demande/resiliation/index.html.twig', ["demandes" => $demandes])->getContent()
        ];
        return new JsonResponse($response);*/
    }


    /**
     * @Route("/valid/{id}", name="valid-demande-resil")
     */
    public function valideDemande(Request $request, $id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_VALID_DEMANDE_RESIL'),
            'id' => $id,
            'motif' => $request->request->get('motif'), //Police.resiliationMotif
            'validationType' => $request->request->get('validationType'), //1, 2 ou 3
            'montantRemboursement' => $request->request->get('montantRemboursement'),
            'dateFin' => $request->request->get('dateFin'), //Champs date manuel
        ]);
        if ($output->status == 200) {
            $this->addFlash('success', 'La demande a bien été acceptée !');
        } else {
            $this->addFlash('error', 'Un problème est survenu...');
        }
        return $this->redirectToRoute('resiliations');
    }

    /**
     * @Route("/pm/{id}", name="pm-demande-resil")
     */
    public function pieceManquante(Request $request, $id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_PIECE_MANQUANTE_DEMANDE_RESIL'),
            'id' => $id,
        ]);
        if ($output->status == 200) {
            $this->addFlash('success', 'La demande a bien été mise en attente....');
        } else {
            $this->addFlash('error', 'Un problème est survenu...');
        }
        return $this->redirectToRoute('resiliations');
    }


    /**
     * @Route("/decline/{id}", name="decline-demande-resil")
     */
    public function declineDemande(Request $request, $id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_DECLINE_DEMANDE_RESIL'),
            'id' => $id
        ]);
        if ($output->status == 200)
            $this->addFlash('success', 'La demande a bien été refusée !');
        else
            $this->addFlash('error', 'Un problème est survenu...');
        return $this->redirectToRoute('resiliations');
    }
}