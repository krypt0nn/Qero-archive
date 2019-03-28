<?php

namespace Qero\Requester;

use Qero\Printer\Printer;

class Requester
{
    /**
     * GET запрос
     * 
     * @param string $url - URL для запроса
     * @return mixed - возвращает результат запроса
     * 
     */

    public static function getRequest ($url, $useProgressBar = false)
    {
        if (extension_loaded ('curl') && $curl = curl_init ($url))
        {
            $progressBar = false;

            if ($useProgressBar)
                $progressBar = new \Console_ProgressBar ('   Downloading... [%bar%] %percent%; Elapsed: %elapsed%', '=>', ' ', 100, 100, array ());

            curl_setopt_array ($curl, array (
                CURLOPT_HEADER         => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_NOPROGRESS     => false,

                CURLOPT_PROGRESSFUNCTION => function ($t, $download_size, $downloaded, $upload_size, $uploaded) use (&$progressBar)
                {
                    if ($progressBar)
                        $progressBar->update (ceil ($downloaded / $download_size * 100));
                },

                CURLOPT_HTTPHEADER => array
                (
                    'User-Agent: PHP'
                )
            ));

            $response = curl_exec ($curl);
            curl_close ($curl);

            if ($useProgressBar)
            {
                $progressBar->update (100);

                fwrite (STDOUT, "\n");
            }

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
