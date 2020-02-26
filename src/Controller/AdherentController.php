<?php

namespace App\Controller;



use App\Entity\User;
use App\Service\Api;
use App\Service\Rendering;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/adherents")
 */
class AdherentController extends AbstractController
{
    /**
     * @Route("/", name="adherents" ,methods={"GET"})
     */
    public function index()
    {
        return $this->render('adherent/search.html.twig');
        /*$response = array(
            "code" => 200,
            "response" => $this->render('adherent/search.html.twig')->getContent());
        return new JsonResponse($response);*/
    }

    /**
     * @Route("/search",name="search_adherent", methods={"POST"})
     */
    public function searchAdherent(Request $request, Api $api)
    {
        $adherents = array();
        if ($search = $request->get("search", "")) {
            if($this->isGranted('ROLE_PARTNER')){
                $produits = $this->getUser()->getPartner()->getProduits();
                $adherents = $api->get([
                    'token' => getenv('TOKEN_LIST_ADHERENT'),
                    'search' => $search,
                    'produits' => serialize($produits)
                ])->adherents;
             /*   foreach ($adherents as $adherent){
                   unset($adherent);
                }*/
            } else {
                $adherents = $api->get([
                    'token' => getenv('TOKEN_LIST_ADHERENT'),
                    'search' => $search
                ])->adherents;
            }
            $response = array(
                "code" => 200,
                "response" => $this->render('adherent/liste.html.twig', ['adherents' => $adherents])->getContent()
            );
            return new JsonResponse($response);
        }
    }


    /**
     * @Route("/view/{id}", name="view_adherent", methods={"GET"})
     */
    public function fiche(Request $request, string $id, Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_ADHERENT'),
            'id' => $id,
        ]);
        if ($output->status == "200") {
            $adherent = $output->assure;
            $impayes = $output->impayes;
            $adhesions = $api->get([
                'token' => getenv('TOKEN_ADHERENT_ADHESIONS'),
                'id' => $id
            ])->adhesions;
            $quotations = $api->get([
                'token' => getenv('TOKEN_GET_QUOTATIONS'),
                'id' => $id
            ])->quotations;
            $response = array(
                "code" => 200,
                "response" => $this->render('adherent/fiche.html.twig', ["adherent" => $adherent, "adhesions" => $adhesions, "quotations" => $quotations,'impayes'=>$impayes])->getContent()
            );
            return new JsonResponse($response);
        } else {
            return $this->redirectToRoute('adherent_liste');
        }
    }

    /**
     * @Route("/adh/view/{id}/adhesion/{idAdhesion}", name="adherent_detail_adhesion")
     */
    public function detailAdhesion(string $id, string $idAdhesion,Api $api)
    {

        $output = $api->get([
            'token' => getenv('TOKEN_ADHESION'),
            'id' => $id,
            'idAdhesion' => $idAdhesion
        ]);
        if ($output->status == "200") {
            $risques = array();
            if($output->adhesion->risques) $risques = (array) $output->adhesion->risques;
            return $this->render('adherent/adhesion.html.twig', ["id" => $id, "adhesion" => $output->adhesion, "risques" => $risques]);
        } else {
            return $this->redirectToRoute('adherent_liste');
        }
    }

    /**
     * @Route("/{id}/adhesion/{idAdhesion}/contrat", name="adherent_genere_contrat")
     */
    public function genereContrat(string $id, string $idAdhesion,Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_GET_SIGNED_CONTRACT'),
            'id' => $id,
            'idAdhesion' => $idAdhesion,
        ]);
        if ($output->status == "200") {
            $response = new Response();
            $response->headers->set('Content-type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename=contrat.pdf');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->setContent(base64_decode($output->file));
            return $response;
        } else {
            return $this->redirectToRoute('profile_login');
        }
    }

    /**
     * @Route("/{id}/adhesion/{idAdhesion}/attestation-responsabilite-civile", name="adherent_genere_attestation")
     */
    public function genereRC(string $id, string $idAdhesion,Api $api, Request $request)
    {
        $lang = "fr";
        $full = 0;
        if ($request->get("lang")){
            if (in_array($request->get("lang"), array("fr", "en", "es"))){
                $lang = $request->get("lang");
            }
        }
        if ($request->get("full")){
            $full = $request->get("full");
        }
        $output = $api->get([
            'token' => getenv('TOKEN_GET_ATTESTATION_RC'),
            'id' => $id,
            'idAdhesion' => $idAdhesion,
            'lang' => $lang,
            'full' => $full
        ]);
        if ($output->status == "200") {
            $response = new Response();
            $response->headers->set('Content-type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename=attestation-responsabilite-civile.pdf');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->setContent(base64_decode($output->file));
            return $response;
        } else {
            return $this->redirectToRoute('profile_login');
        }
    }

    /**
     * @Route("/{id}/modifier", name="adherent_update_infos")
     */
    public function updateInformation(string $id, Request $request, CsrfTokenManagerInterface $csrfTokenManager, Rendering $rendering,Api $api)
    {
        $output = $api->get([
            'token' => getenv('TOKEN_ADHERENT'),
            'id' => $id
        ]);
        if ($output->status == "200") {
            $adherent = $output->assure;
        } else {
            return $this->redirectToRoute('profile_login');
        }
        $errors = array();
        if ($token = $request->get('csrf_token')) {
            $token = new CsrfToken('updateAccountInformations', $token);
            if (!$csrfTokenManager->isTokenValid($token)) {
                $errors[] = "Invalid token";
            }
            if (!$adherent->address = $request->get('address')) {
                $errors[] = "Empty address";
            }
            $adherent->addressComplementary = $request->get('addressComplementary');
            if (!$adherent->postalCode = $request->get('postalCode')) {
                $errors[] = "Empty postalCode";
            }
            if (!$adherent->city = $request->get('city')) {
                $errors[] = "Empty city";
            }
            if (!$adherent->country = $request->get('country')) {
                $errors[] = "Empty country";
            }
            if (count($errors) == 0) {
               $output = $api->get([
                   'token' => getenv('TOKEN_MODIF_ADHERENT'),
                   'id' => $id,
                   'address' => $adherent->address,
                   'addressComplementary' => $adherent->addressComplementary,
                   'postalCode' => $adherent->postalCode,
                   'city' => $adherent->city,
                   'country' => $adherent->country
               ]);
                if ($output->status == "200") {
                    $response = array(
                        "code" => 200,
                        "idAdh" => $id,
                        "response" => $rendering->block('adherent/fiche.html.twig', 'modalbody', ['adherent' => $adherent])
                    );
                    return new JsonResponse($response);
                } else {
                    $errors[] = $output->message;
                }
            }
        }
        $response = array(
            "code" => 200,
            "response" => $this->render('adherent/update_infos.html.twig', ["adherent" => $adherent, "errors" => $errors])->getContent()
        );
        return new JsonResponse($response);
    }
}
