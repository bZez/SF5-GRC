<?php

namespace App\Controller;

use App\Service\Api;
use App\Service\Count;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/devis")
 */
class QuotationController extends AbstractController
{

    /**
     * @Route("/", name="devis")
     */
    public function listQuotations(Api $api,Count $count)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $output = $api->get(getenv('TOKEN_GET_QUOTATIONS'));
        $quotations = $output->quotations;
        return $this->render('quotation/liste.html.twig', ["quotations" => $quotations,"count"=>$count->quotations()]);
        /*
        $response = array(
            "code" => 200,
            "response" => $this->render('quotation/liste.html.twig', ["quotations" => $quotations,"count"=>$count->quotations()])->getContent());
        return new JsonResponse($response);*/
    }

    /**
     * @Route("/update/{id}", name="quotation_update")
     */
    public function updateQuotations(Request $request, Api $api,$id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $errors = array();
        $output = $api->get([
            'token' => getenv('TOKEN_GET_QUOTATION'),
            "id" => $id
        ]);
        $adherent = $output->assure;
        $quotation = $output->quotation;
        $risques = (array) $output->quotation->risques;

        $output = $api->get(getenv('TOKEN_LIST_PRODUITS'));
        $products = $output->produits;
        $formules = [];
        $dates = [];
        foreach ($products as $product)
        {
            $req = $api->get([
                'token' => getenv('TOKEN_LIST_FORMULE'),
                'produit' => $product->id
            ]);
            array_push($formules,$req);
            array_push($dates,$req->dates);
        }


        return $this->render('quotation/update_quotation.html.twig', ["errors" => $errors, "products" => $products, "warranties" => $formules, "risques" => $risques, "dates" =>$dates, "quotation" => $quotation, "adherent" => $adherent,"idDevis" => $id]);
    }
}
