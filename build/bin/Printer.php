<?php

namespace Qero\Printer;

class Printer
{
    /**
     * Print message to console
     * 
     * @param string $message - message
     * [@param int $state = 0] - type of message:
     *   0 - default output
     *   1 - info
     *   2 - error
     * 
     *   default value: 0
     * 
     */

    public static function say ($message, $state = 0)
    {
        if (defined ('STDOUT') && defined ('STDERR'))
        {
            $message = str_replace ("\n", "\n ", $message);

            switch ($state)
            {
                case 1:
                    fwrite (STDERR, ' [!] '. $message .PHP_EOL);
                break;

                case 2:
                    fwrite (STDOUT, ' [*] '. $message .PHP_EOL);
                break;

                case 0:
                default:
                    fwrite (STDOUT, ' '. $message .PHP_EOL);
                break;
            }
        }
    }
}

?>
