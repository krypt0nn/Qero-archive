<?php

namespace Qero\Controller;

use Qero\Exceptions\Exception;
use Qero\Printer\Printer;
use Qero\PackagesManager\PackagesManager;

define ('QERO_FOOTER', '
'. "\x1b[36;1m" .'
      ___           ___           ___           ___     
     /\  \         /\  \         /\  \         /\  \    
    /::\  \       /::\  \       /::\  \       /::\  \   
   /:/\:\  \     /:/\:\  \     /:/\:\  \     /:/\:\  \  
   \:\~\:\  \   /::\~\:\  \   /::\~\:\  \   /:/  \:\  \ 
    \:\ \:\__\ /:/\:\ \:\__\ /:/\:\ \:\__\ /:/__/ \:\__\
     \:\/:/  / \:\~\:\ \/__/ \/_|::\/:/  / \:\  \ /:/  /
      \::/  /   \:\ \:\__\      |:|::/  /   \:\  /:/  / 
      /:/  /     \:\ \/__/      |:|\/__/     \:\/:/  /  
     /:/  /       \:\__\        |:|  |        \::/  /   
     \/__/         \/__/         \|__|         \/__/    
'. "\x1b[35;1m" .'
     Author: Observer KRypt0n_
        vk.com/technomindlp
        vk.com/hphp_convertation
'. "\x1b[36;1m" .'
     Qero '. QERO_VERSION .'
'. "\x1b[30;1m" .'
     Copyright © 2018 - 2019 Podvirnyy Nikita (KRypt0n_)
     This program comes with ABSOLUTELY NO WARRANTY;
     This is free software, and you are welcome to redistribute it under certain conditions;
     lookup <https://github.com/KRypt0nn/Qero/license.txt> for details.
'. "\x1b[0m" .'
');

define ('QERO_HELP', '

    Qero.phar [command] [args]
    Example: Qero.phar install php-ai/php-ml

    Commands:
        help                   - print Qero commands list
        install [repos list]   - downloading requirement to the folder
        delete [packages list] - deleting installed packages (with package source)
        update                 - updating (re-installing) all installed packages
        packages               - print installed packages list

');

class Controller
{
    public $manager;

    public function __construct ()
    {
        $this->manager = new PackagesManager;
    }

    /**
     * Выполнение команды консоли
     * 
     * @param array $args - список аргументов консоли
     * [@param int $argc = null] - кол-во аргументов
     * 
     */

    public function executeCommand ($args, $argc = null)
    {
        if ($argc === null)
            $argc = sizeof ($args);
        
        switch ($args[1])
        {
            case 'help':
                $this->printHelp ();
            break;

            case 'install':
                if ($argc < 3)
                    throw new Exception ('Installing repository isn\'t selected');

                foreach (array_slice ($args, 2) as $repository)
                    $this->manager->installPackage ($repository);

                Printer::say ("\n\x1b[32;1mInstalling complited\x1b[0m");
            break;

            case 'delete':
                if ($argc < 3)
                    throw new Exception ('Deleting packages isn\'t selected');

                foreach (array_slice ($args, 2) as $package)
                {
                    Printer::say ('Deleting '. "\x1b[33;1m$package\x1b[0m" .'...');

                    $this->manager->deletePackage ($package);
                }

                Printer::say ("\n\x1b[32;1mDeleting complited\x1b[0m");
            break;

            case 'update':
                $this->manager->updatePackages ();

                Printer::say ("\n\x1b[32;1mUpdating complited\x1b[0m");
            break;

            case 'packages':
                if (isset ($this->manager->settings['packages']) && sizeof ($this->manager->settings['packages']) > 0)
                    Printer::say ("Installed packages:\n\n". implode ("\n", array_map (function ($package)
                    {
                        return isset ($this->manager->settings['packages'][$package]['version']) ?
                            "\x1b[33;1m$package\x1b[0m" .' (version: '. $this->manager->settings['packages'][$package]['version'] .')' :
                            "\x1b[33;1m$package\x1b[0m";
                    }, array_keys ($this->manager->settings['packages']))));

                else Printer::say ('No one package installed');
            break;

            default:
                Printer::say ('Using unknown command "'. $args[1] .'"', 2);
            break;
        }
    }

    /**
     * Вывод шапки программы
     */

    public function printFooter ()
    {
        Printer::say (QERO_FOOTER);
    }

    /**
     * Вывод помощи (списка команд)
     */

    public function printHelp ()
    {
        Printer::say (QERO_HELP);
    }
}

?>
