<?php

namespace App\Service;


class Count
{
    public function __construct(Api $api)
    {
        $this->api = $api;
    }
    public function counter($produit)
    {
        $output = $this->api->get([
            'token' => getenv('TOKEN_COUNT_ADHERENTS'),
            'produit' => $produit,
        ]);
        return $output;
    }


    public function quotations()
    {
        $output = $this->api->get(getenv('TOKEN_GET_QUOTATIONS'));
        return count($output->quotations);
    }
    public function retractations()
    {
        $output = $this->api->get(getenv('TOKEN_LIST_DEMANDE_RETRAC'));
        return count($output->result);
    }
    public function resiliations()
    {
        $output = $this->api->get(getenv('TOKEN_LIST_DEMANDE_RESIL'));
        return count($output->result);
    }
    public function teletransmissions()
    {
        $output = $this->api->get(getenv('TOKEN_LIST_DEMANDE_TELETRANS'));
        return count($output->result);
    }
    public function pec()
    {
        $output = $this->api->get(getenv('TOKEN_LIST_DEMANDES_PEC'));
        return count($output->result);
    }
    public function remboursements()
    {
        $output = $this->api->get(getenv('TOKEN_LIST_DEMANDES_REMB'));
        return count($output->result);
    }
    public function mobints()
    {
        $output = $this->api->get(getenv('TOKEN_LIST_DEMANDES_MOBINT'));
        return count($output->result);
    }
}