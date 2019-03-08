<?php

namespace Qero\Printer;

class Printer
{
    /**
     * Вывод сообщения в консоль
     * 
     * @param string $message - сообщение
     * [@param int $state = 0] - тип сообщения:
     *   0 - стандартное
     *   1 - информация
     *   2 - ошибка
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
