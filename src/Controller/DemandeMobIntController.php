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
 * @Route("/demandes/mobilite-internationale")
 */
class DemandeMobIntController extends AbstractController
{
    /**
     * @Route("/", name="demandes_mobint")
     */
    public function indexMOBINT(Api $api)
    {
        $demandes = $api->get(getenv('TOKEN_LIST_DEMANDES_MOBINT'))->result;
        return $this->render('demande/mobint/index.html.twig',['demandes'=>$demandes]);
        /*
        $response = [
            'code' => 200,
            'response' => $this->render('demande/mobint/index.html.twig',['demandes'=>$demandes])->getContent()
        ];
        return new JsonResponse($response);*/

    }

    /**
     * @Route("/download/{id}/{file}", name="download_demande_file")
     */
    public function downloadDemandeFile(Request $request, $id,Api $api,$file)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_SHOW_DEMANDE_MOBINT'),
            'id' => $id
        ]);
        $base64 = $output->files[$file]->content;
        $binary = base64_decode($base64);


        $response = new Response();

        $extension = explode(".", $output->files[$file]->name);
        $extension = strtolower(end($extension));
        if($extension == "pdf"){
            $response->headers->set('Content-Type', 'application/pdf');
        }else if($extension == "jpg" || $extension == "jpeg"){
            $response->headers->set('Content-Type', 'image/jpeg');
        }else if($extension == "png"){
            $response->headers->set('Content-Type', 'image/png');
        }else if($extension == "gif"){
            $response->headers->set('Content-Type', 'image/gif');
        }
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $output->files[$file]->name);

        $response->setContent($binary);

        return $response;

    }

    /**
     * @Route("/valid/{id}", name="valid-demande-mobint")
     */
    public function validDemande($id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_VALID_DEMANDE_MOBINT'),
            'id' => $id
        ]);
        if ($output->status == 200)
            $this->addFlash('success', 'La demande a bien été validée !');
        else
            $this->addFlash('error', 'Un problème est survenu...');
        return $this->redirectToRoute('demandes_mobint');
    }

    /**
     * @Route("/decline/{id}", name="decline-demande-mobint")
     */
    public function declineDemande($id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_DECLINE_DEMANDE_MOBINT'),
            'id' => $id
        ]);
        if ($output->status == 200)
            $this->addFlash('success', 'La demande a bien été refusée !');
        else
            $this->addFlash('error', 'Un problème est survenu...');
        return $this->redirectToRoute('demandes_mobint');
    }

}