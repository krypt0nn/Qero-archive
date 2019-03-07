<?php

namespace Qero\Controller;

use Qero\{
    Exceptions\Exception,
    Printer\Printer,
    PackagesManager\PackagesManager
};

const QERO_FOOTER = '

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

     Author: Observer KRypt0n_
        vk.com/technomindlp
        vk.com/hphp_convertation

     Version: '. \Qero\QERO_VERSION .'


';

const QERO_HELP = '

    Qero.phar [command] [args]
    Example: Qero.phar install php-ai/php-ml

    Commands:
        help                   - print Qero commands list
        install [repos list]   - downloading requirement to the folder
        delete [packages list] - deleting installed packages
        update                 - updating (re-installing) all installed packages

';

class Controller
{
    public $manager;

    public function __construct ()
    {
        $this->manager = new PackagesManager;
    }

    public function executeCommand (array $args, int $argc = null): void
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

                Printer::print ("\nInstalling complited");
            break;

            case 'delete':
                if ($argc < 3)
                    throw new Exception ('Deleting packages isn\'t selected');

                foreach (array_slice ($args, 2) as $package)
                {
                    Printer::print ('Deleting "'. $package .'"...');

                    $this->manager->deletePackage ($package);
                }

                Printer::print ("\nDeleting complited");
            break;

            case 'update':
                $this->manager->updatePackages ();
            break;

            default:
                Printer::print ('Using unknown command "'. $args[1] .'"', 2);
            break;
        }
    }

    public function printFooter (): void
    {
        Printer::print (QERO_FOOTER);
    }

    public function printHelp (): void
    {
        Printer::print (QERO_HELP);
    }
}

?>
