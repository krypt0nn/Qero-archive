<?php

namespace Qero;

define ('QERO_HEADER', '
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
        upgrade                - upgrade Qero to actual version

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

                Printer::say (Printer::color ("\n\x1b[32;1m") .'Installing completed'. Printer::color ("\x1b[0m"));
            break;

            case 'remove':
                if ($argc < 3)
                    throw new Exception ('Removing packages isn\'t selected');

                foreach (array_slice ($args, 2) as $package)
                {
                    Printer::say ('Removing '. Printer::color ("\x1b[33;1m") . $package . Printer::color ("\x1b[0m") .'...');

                    $this->manager->removePackage ($package);
                }

                Printer::say (PHP_EOL . Printer::color ("\x1b[32;1m") .'Removing completed'. Printer::color ("\x1b[0m"));
            break;

            case 'update':
                if (sizeof ($this->manager->packages) > 0)
                {
                    $this->manager->updatePackages ();

                    Printer::say (PHP_EOL . Printer::color ("\x1b[32;1m") .'Updating completed'. Printer::color ("\x1b[0m"));
                }

                else Printer::say ('No one package installed', 2);
            break;

            case 'upgrade':
                if (file_exists ($qero = QERO_DIR .'/'. ($qeroDir = 'qero-'. substr (sha1 (microtime (true) . rand (1, 9999999999)), 0, 8)) .'.tar'))
                    unlink ($qero);

                $archive = new \Qero\Sources\GitHub ('KRypt0nn/Qero');
                file_put_contents ($qero, $archive->getPackageArchive ());

                dir_delete ($qeroDir = QERO_DIR .'/'. $qeroDir);
                mkdir ($qeroDir);

                Printer::say ('  Unpacking...');
        
                $archive = new \PharData ($qero);
                $archive->extractTo ($qeroDir, null, true);
                unset ($archive);
                \PharData::unlinkArchive ($qero);

                foreach (array_slice (scandir ($qeroDir), 2) as $dir)
                    if (is_dir ($qeroDir .'/'. $dir))
                    {
                        $qeroDir .= '/'. $dir;

                        break;
                    }

                (new \Phar ('Qero.phar'))->buildFromDirectory ($qeroDir .'/build');
                dir_delete (dirname ($qeroDir));

                Printer::say (PHP_EOL . Printer::color ("\x1b[32;1m") .'Upgrading completed'. Printer::color ("\x1b[0m"));
            break;

            case 'packages':
                if (isset ($this->manager->packages) && sizeof ($this->manager->packages) > 0)
                    Printer::say ('Installed packages:'. PHP_EOL . PHP_EOL . implode (PHP_EOL, array_map (function ($package)
                    {
                        return ' - '. (isset ($this->manager->packages[$package]->version) ?
                            Printer::color ("\x1b[33;1m") . $package . Printer::color ("\x1b[0m") .' (version: '. $this->manager->packages[$package]->version .')' :
                            Printer::color ("\x1b[33;1m") . $package . Printer::color ("\x1b[0m"));
                    }, array_keys ($this->manager->packages))));

                else Printer::say ('No one package installed', 2);
            break;

            case 'rebuild':
                if (!isset ($this->manager->packages) || sizeof ($this->manager->packages) == 0)
                    Printer::say ('No one package installed'. PHP_EOL, 2);

                Printer::say ('Rebuilding "autoload.php"...');
                AutoloadGenerator::generateAutoload ();
            break;

            default:
                if (file_exists (QERO_DIR .'/qero-info.json'))
                {
                    $info = json_decode (file_get_contents ('qero-info.json'), true);

                    if (isset ($info['scripts'][$args[1]]))
                    {
                        Printer::say ('> '. $info['scripts'][$args[1]]);
                        Printer::say (shell_exec ($info['scripts'][$args[1]]));

                        return;
                    }
                }
                
                Printer::say ('Using unknown command "'. $args[1] .'"', 1);
            break;
        }
    }

    /**
     * Вывод шапки программы
     */
    public function printHeader ()
    {
        Printer::say (QERO_HEADER);
    }

    /**
     * Вывод помощи (списка команд)
     */
    public function printHelp ()
    {
        $this->printHeader ();

        Printer::say (QERO_HELP);
    }
}
