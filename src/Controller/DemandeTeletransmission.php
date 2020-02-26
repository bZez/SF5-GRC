<?php


namespace App\Controller;


use App\Security\Tools;
use App\Service\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demandes/teletransmission")
 */
class DemandeTeletransmission extends AbstractController
{

    /**
     * @Route("/", name="teletransmissions")
     */
    public function index(Api $api)
    {
        $demandes = $api->get(getenv('TOKEN_LIST_DEMANDE_TELETRANS'))->result;
        return $this->render('demande/teletransmission/index.html.twig', ["demandes" => $demandes]);
        /*
        $response = [
            'code' => 200,
            'response' => $this->render('demande/teletransmission/index.html.twig', ["demandes" => $demandes])->getContent()
        ];
        return new JsonResponse($response);*/
    }


    /**
     * @Route("/show/{id}", name="show-demande-teletransmission")
     */
    public function showDemande(Api $api, $id)
    {
        $demande = $api->get([
            'token' => getenv('TOKEN_SHOW_DEMANDE_TELETRANS'),
            'id' => $id
        ])->result;
        $response = [
            'code' => 200,
            'response' => $this->render('demande/teletransmission/show.html.twig', ["demande" => $demande])->getContent()
        ];
        return new JsonResponse($response);
    }


    /**
     * @Route("/download/{id}", name="download_demande_teletransmission")
     */
    public function downloadDemande(Request $request, $id,Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_SHOW_DEMANDE_TELETRANS'),
            'id' => $id
        ]);
        $base64 = $output->files[0]->content;
        $binary = base64_decode($base64);




        $response = new Response();
        $extension = explode(".", $output->files[0]->name);
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
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $output->files[0]->name);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($binary);
        return $response;

    }

    /**
     * @Route("/valid/{id}", name="valid_demande_teletransmission")
     */
    public function validDemande(Request $request, $id,Api $api)
    {


        $output = $api->get([
                'token' => getenv('TOKEN_VALID_DEMANDE_TELETRANS'),
                'insee' => str_replace(' ','',$request->request->get('insee')),
                'inseeKey' => $request->request->get('inseeKey'),
                'iban' => $request->request->get('iban'),
                'teletransRegime' => $request->request->get('teletransRegime'), //Lenght 2
                'teletransCaisse' => $request->request->get('teletransCaisse'),
                'teletransCentre' => $request->request->get('teletransCentre'),
                'id' => $id
            ]);


        if (!$output->status == 200) {

            die("problem");
        }

        $this->addFlash('success', 'La demande a bien été validé ');
        return $this->redirectToRoute('teletransmissions');

    }


}