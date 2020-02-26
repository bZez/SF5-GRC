<?php

namespace App\Controller;

use App\Service\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/demandes")
 */
class DemandeContoller extends AbstractController
{
    /**
     * @Route("/pec", name="demandes_pec")
     */
    public function indexPEC(Api $api)
    {
        $demandes = $api->get(getenv('TOKEN_LIST_DEMANDES_PEC'))->result;
        return $this->render('demande/all/index.html.twig',['demandes'=>$demandes]);
            /*
        $response = [
            'code' => 200,
            'response' => $this->render('demande/all/index.html.twig',['demandes'=>$demandes])->getContent()
        ];
        return new JsonResponse($response);*/

    }

    /**
     * @Route("/remboursement", name="demandes_remb")
     */
    public function indexREMB(Api $api)
    {
        $demandes = $api->get(getenv('TOKEN_LIST_DEMANDES_REMB'))->result;
        return $this->render('demande/all/index.html.twig',['demandes'=>$demandes]);
        /*
    $response = [
        'code' => 200,
        'response' => $this->render('demande/all/index.html.twig',['demandes'=>$demandes])->getContent()
    ];
    return new JsonResponse($response);*/

    }

    /**
     * @Route("/download", name="demandes_download")
     */
    public function downloadFile(Api $api, Request $request)
    {
        if(!$fileName = $request->get("filename")){
            die("missing parametter");
        }
        $output = $api->get([
            'token' => getenv('TOKEN_DOWNLOAD_FILE'),
            'fileName' => $fileName
        ]);

        if($output->status != 200){
            die("file not found");
        }
        $base64 = $output->content;
        $binary = base64_decode($base64);

        $response = new Response();
        $extension = explode(".", $output->name);
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
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $output->name);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($binary);
        return $response;

    }


}