<?php

namespace Qero\Printer;

class Printer
{
    static $print = true;
    static $canUseColors = null;

    /**
     * Вывод сообщения в консоль
     * 
     * @param string $message - сообщение
     * [@param int $state = 0] - тип сообщения:
     *   0 - стандартное
     *   1 - ошибка
     *   2 - информация
     * 
     *   default value: 0
     * 
     */

    public static function say ($message, $state = 0)
    {
        if (defined ('STDOUT') && defined ('STDERR') && self::$print)
        {
            $message = str_replace ("\n", "\n ", $message);

            switch ($state)
            {
                case 1:
                    fwrite (STDERR, self::color ("\x1b[31;1m") .' [!]'. self::color ("\x1b[0m") ." $message" .PHP_EOL);
                break;

                case 2:
                    fwrite (STDOUT, self::color ("\x1b[33;1m") .' [*]'. self::color ("\x1b[0m") ." $message" .PHP_EOL);
                break;

                case 0:
                default:
                    fwrite (STDOUT, ' '. $message .PHP_EOL);
                break;
            }
        }
    }

    /**
     * Корректор цвета текста консоли
     * 
     * @param string $color - возвращаемое значение в случае если система поддерживает ASCII colors
     * @return string
     * 
     */

    public static function color ($color)
    {
        if (self::$canUseColors === null)
        {
            if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN')
                self::$canUseColors = strpos (php_uname ('v'), '(Windows 10)') !== false;

            else self::$canUseColors = true;
        }

        return self::$canUseColors ? $color : '';
    }
}
