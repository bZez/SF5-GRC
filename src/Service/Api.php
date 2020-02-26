<?php

namespace App\Service;


class Api
{
    public function get($flds,$die=null)
    {
        if (is_array($flds))
            $fields = $flds;
        else
            $fields = array(
                'token' => $flds
            );
        $params = array(
            CURLOPT_URL => getenv('API_URL'),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_SSL_VERIFYPEER => false
        );
        $ch = curl_init();
        curl_setopt_array($ch, $params);
        $output = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);
        if($die){
            die($output);
        }
        $output = json_decode($output);
        return $output;
    }
}