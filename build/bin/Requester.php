<?php

namespace Qero\Requester;

class Requester
{
    /**
     * GET запрос
     * 
     * @param string $url - URL для запроса
     * @return mixed - возвращает результат запроса
     * 
     */

    public static function getRequest ($url)
    {
        if (extension_loaded ('curl') && $curl = curl_init ($url))
        {
            curl_setopt_array ($curl, array (
                CURLOPT_HEADER         => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,

                CURLOPT_HTTPHEADER => array
                (
                    'User-Agent: PHP'
                )
            ));

            $response = curl_exec ($curl);

            curl_close ($curl);

            return $response;
        }

        return file_get_contents ($url, false, stream_context_create (array (
            'ssl' => array
            (
                'verify_peer'      => false,
                'verify_peer_name' => false
            ),

            'http' => array
            (
                'method' => 'GET',
                'header' => array ('User-Agent: PHP')
            )
        )));
    }
}

?>
