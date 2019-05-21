<?php

namespace Qero\Controller;

use Qero\Exceptions\Exception;
use Qero\Printer\Printer;
use Qero\PackagesManager\PackagesManager;
use Qero\AutoloadGenerator\AutoloadGenerator;

define ('QERO_FOOTER', '
'. Printer::color ("\x1b[36;1m") .'
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
'. Printer::color ("\x1b[35;1m") .'
     Author: Observer KRypt0n_
        vk.com/technomindlp
        vk.com/hphp_convertation
'. Printer::color ("\x1b[36;1m") .'
     Qero '. QERO_VERSION .'
'. Printer::color ("\x1b[30;1m") .'
     Copyright © 2018 - 2019 Podvirnyy Nikita (KRypt0n_)
     This program comes with ABSOLUTELY NO WARRANTY;
     This is free software, and you are welcome to redistribute it under certain conditions;
     lookup <https://github.com/KRypt0nn/Qero/license.txt> for details.
'. Printer::color ("\x1b[0m") .'
');

define ('QERO_HELP', '

    Qero.phar [command] [args]
    Example: Qero.phar install php-ai/php-ml

    Commands:
        help                   - print Qero commands list
        install [*repos list]  - downloading repositories to the folder
        remove [packages list] - remove installed packages (with package source)
        update                 - updating (re-installing) all installed packages
        packages               - print installed packages list
        rebuild                - rebuild "qero-packages/autoload.php" file

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
                {
                    if (file_exists (QERO_DIR .'/qero-info.json'))
                    {
                        $info = json_decode (file_get_contents ('qero-info.json'), true);

                        if (isset ($info['requires']))
                            $args = array_merge ($args, $info['requires']);
                    }

                    else throw new Exception ('Installing repository isn\'t selected');
                }

                foreach (array_slice ($args, 2) as $repository)
                    $this->manager->installPackage ($repository);

                Printer::say (Printer::color ("\n\x1b[32;1m") .'Installing complited'. Printer::color ("\x1b[0m"));
            break;

            case 'remove':
                if ($argc < 3)
                    throw new Exception ('Removing packages isn\'t selected');

                foreach (array_slice ($args, 2) as $package)
                {
                    Printer::say ('Removing '. Printer::color ("\x1b[33;1m") . $package . Printer::color ("\x1b[0m") .'...');

                    $this->manager->removePackage ($package);
                }

                Printer::say (PHP_EOL . Printer::color ("\x1b[32;1m") .'Removing complited'. Printer::color ("\x1b[0m"));
            break;

            case 'update':
                if (isset ($this->manager->settings['packages']) && sizeof ($this->manager->settings['packages']) > 0)
                {
                    $this->manager->updatePackages ();

                    Printer::say (PHP_EOL . Printer::color ("\x1b[32;1m") .'Updating complited'. Printer::color ("\x1b[0m"));
                }

                else Printer::say ('No one package installed', 2);
            break;

            case 'packages':
                if (isset ($this->manager->settings['packages']) && sizeof ($this->manager->settings['packages']) > 0)
                    Printer::say ('Installed packages:'. PHP_EOL . PHP_EOL . implode (PHP_EOL, array_map (function ($package)
                    {
                        return isset ($this->manager->settings['packages'][$package]['version']) ?
                            Printer::color ("\x1b[33;1m") . $package . Printer::color ("\x1b[0m") .' (version: '. $this->manager->settings['packages'][$package]['version'] .')' :
                            Printer::color ("\x1b[33;1m") . $package . Printer::color ("\x1b[0m");
                    }, array_keys ($this->manager->settings['packages']))));

                else Printer::say ('No one package installed', 2);
            break;

            case 'rebuild':
                Printer::say ('Rebuilding "autoload.php"...');

                AutoloadGenerator::generateAutoload ();
            break;

            default:
                Printer::say ('Using unknown command "'. $args[1] .'"', 1);
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
        $this->printFooter ();

        Printer::say (QERO_HELP);
    }
}
