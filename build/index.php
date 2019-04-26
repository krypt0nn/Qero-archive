<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     Qero
 * @copyright   2018 - 2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

namespace Qero;

define ('QERO_VERSION', '2.7.1');

/**
 * Рекурсивное удаление директории и всех последующих директорий и файлов
 * 
 * @param string $path - директория для удаления
 * 
 */

date_default_timezone_set ('UTC');

function dir_delete ($path)
{
    if (!is_dir ($path))
        return false;

    foreach (array_slice (scandir ($path), 2) as $file)
        if (is_dir ($file = $path .'/'. $file))
        {
            dir_delete ($file);

            if (is_dir ($file))
                rmdir ($file);
        }

        else unlink ($file);

    rmdir ($path);

    return true;
}

define ('QERO_DIR', dirname (substr (__DIR__, 0, 7) == 'phar://' ? substr (__DIR__, 7) : __DIR__));

if (!is_dir (QERO_DIR .'/qero-packages'))
    mkdir (QERO_DIR .'/qero-packages');

require 'exts/ProgressBar.php';
require 'bin/Printer.php';
require 'bin/Exceptions.php';
require 'bin/Controller.php';
require 'bin/Requester.php';
require 'sources/SourceInterface.php';
require 'sources/GitHub.php';
require 'sources/GitLab.php';
require 'sources/BitBucket.php';
require 'bin/AutoloadGenerator.php';
require 'bin/PackagesManager.php';

use Qero\Printer\Printer;
use Qero\Controller\Controller;

global $controller;

$controller = new Controller;

if ($argc <= 1)
{
    $controller->printFooter ();

    Printer::say ('Parameters not selected. Use "Qero help" to see commands list', 1);
}

else
{
    echo PHP_EOL;

    $controller->executeCommand ($argv, $argc);
}

?>
