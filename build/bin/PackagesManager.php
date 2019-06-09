<?php

namespace Qero;

define ('QERO_AUTOGENERATE', '

/*
    Auto generated by Qero '. QERO_VERSION .'
    '. date ('Y/m/d H:i:s') .' (UTC, timestamp: '. time () .')
*/

');

class PackagesManager
{
    /**
     * Массив пакетов
     */
    public $packages = array ();

    /**
     * Список имён начальных файлов для подключение (в порядке понижения приоритета)
     */
    protected $enteringPoints = array
    (
        'qero-init.php',
        'qero-main.php',
        'main.php',
        'index.php',
        'autorun.php',
        'startup.php'
    );

    public function __construct ()
    {
        $packages = file_exists (QERO_DIR .'/qero-packages/packages.json') ?
            json_decode (file_get_contents (QERO_DIR .'/qero-packages/packages.json'), true) : array ();

        foreach ($packages as $name => $package)
            $this->packages[$name] = new Package ($package);
    }

    /**
     * Установка пакета
     * 
     * @param string $package - полное название пакета
     * 
     * @return bool - возвращает статус установки пакета
     */
    public function installPackage ($package, $alreadyInstalled = array ())
    {
        $packageInfo = $this->getPackageBlocks ($package);

        switch ($packageInfo['source'])
        {
            case 'github':
                $source = 'Qero\Sources\GitHub';
            break;

            case 'gitlab':
                $source = 'Qero\Sources\GitLab';
            break;

            case 'bitbucket':
                $source = 'Qero\Sources\BitBucket';
            break;

            default:
                Printer::say ('Source '. Printer::color ("\x1b[33;1m"). $packageInfo['source'] .Printer::color ("\x1b[0m") .' not founded. Skipping...'. PHP_EOL, 1);

                return false;
            break;
        }

        $package = new Package (array (
            'name'      => $packageInfo['full_name'],
            'full_name' => $packageInfo['full_path'],
            'version'   => $packageInfo['version'],
            'source'    => new $source ($packageInfo['full_name'])
        ));

        if (isset ($alreadyInstalled[$packageInfo['full_path']]))
            return false;

        $commit = $package->getCommit ();

        if (isset ($this->packages[$packageInfo['full_path']]))
        {
            if ($commit[$source::$watermark] == $this->packages[$packageInfo['full_path']]->watermark)
            {
                Printer::say ('Repository '. Printer::color ("\x1b[33;1m") . $package->name . Printer::color ("\x1b[0m") .' already installed. Skipping...'. PHP_EOL, 2);

                return false;
            }

            else Printer::say ('Repository '. Printer::color ("\x1b[33;1m") . $package->name . Printer::color ("\x1b[0m") .' already installed, but version is outdated. Updating...'. PHP_EOL, 2);
        }

        Printer::say ('Installing '. Printer::color ("\x1b[33;1m") . $package->name . Printer::color ("\x1b[0m") .'...');

        $package = $package->download ()->register ();

        $this->registerPackage ($package, array_merge ($alreadyInstalled, array ($package->full_name => true)));

        return true;
    }

    /**
     * FIXME
     * 
     * Регистрация нового пакета в менеджере
     * 
     * @param string $package - полное название пакета
     */
    public function registerPackage ($package, $alreadyInstalled = array ())
    {
        $this->packages[$package->full_name] = $package;

        AutoloadGenerator::generateAutoload ();

        if (isset ($package->requires))
            foreach ($package->requires as $repository)
                if (!isset ($this->packages[$repository]))
                {
                    $this->installPackage ($repository, $alreadyInstalled);

                    $alreadyInstalled[] = $repository;
                }

        if ($package->after_install !== null)
            @require_once QERO_DIR .'/qero-packages/'. $package->name .'/'. $package->basefolder .'/'. $package->after_install;
    }

    /**
     * Удаление пакета
     * 
     * @param string $package - полное название пакета
     */
    public function removePackage ($package)
    {
        $packageInfo = $this->getPackageBlocks ($package);
        $delete = false;

        $package = new Package (array (
            'name'      => $packageInfo['full_name'],
            'full_name' => $packageInfo['full_path']
        ));

        if (!is_dir (QERO_DIR .'/qero-packages/'. $packageInfo['full_name']))
        {
            echo PHP_EOL;

            Printer::say ('Package '. Printer::color ("\x1b[33;1m") . $packageInfo['full_name'] . Printer::color ("\x1b[0m") .' not founded. Skipping...'. PHP_EOL, 2);

            return;
        }

        $package->remove ();

        if ($this->packages[$packageInfo['full_path']]->requires !== null && sizeof ($this->packages[$packageInfo['full_path']]->requires) > 0)
            $delete = 'Package '. Printer::color ("\x1b[33;1m") . $packageInfo['full_name'] . Printer::color ("\x1b[0m") .' have '. sizeof ($this->packages[$packageInfo['full_path']]->requires) .' requires. If you are sure to remove them - type:'. PHP_EOL . PHP_EOL .'  Qero.phar remove '. implode (' ', $this->packages[$packageInfo['full_path']]->requires). PHP_EOL;

        unset ($this->packages[$packageInfo['full_path']]);
        
        AutoloadGenerator::generateAutoload ();

        if ($delete)
            Printer::say ($delete, 2);
    }

    /**
     * Обновление всех установленных пакетов
     */
    public function updatePackages ()
    {
        $repos = array_unique (array_keys ($this->packages));
        $alreadyInstalled = array ();

        foreach ($repos as $repository)
        {
            $this->installPackage ($repository, $alreadyInstalled);

            $alreadyInstalled[] = $repository;
        }
    }

    /**
     * Получение списка PHP файлов в директории
     * 
     * @param string $folder - директория для анализа
     * [@param string $basefolder = null] - префикс директории, который будет удалён
     * 
     * @return array - возвращает список PHP файлов
     */
    public function getPhpsList ($folder, $basefolder = null)
    {
        $list = array ();

        if ($basefolder === null)
            $basefolder = $folder .'/';

        foreach (array_slice (scandir ($folder), 2) as $file)
        {
            $ext = explode ('.', $file);

            if (strtolower (end ($ext)) == 'php' && strpos ($file, '.') !== false)
                $list[] = str_replace ($basefolder, '', $folder .'/'. $file);

            elseif (is_dir ($folder .'/'. $file))
                $list = array_merge ($list, $this->getPhpsList ($folder .'/'. $file, $basefolder));
        }

        return $list;
    }

    /**
     * Получение списка зависимостей исходя из их приоритетности
     * 
     * @param array $packages - список пакетов для генерации
     * [@param array $requires = null] - список зависимостей
     * 
     * @return array - возвращает список зависимостей
     */
    public function getRequires ($packages, $requires = null)
    {
        if ($requires === null)
            $requires = array ();

        foreach ($packages as $package)
        {
            $package = $this->getPackageBlocks ($package);
            $package = $package['full_path'];

            if (!isset ($this->packages[$package]->requires))
            {
                if (array_search ($package, $requires) === false)
                    $requires[] = $package;
            }

            else
            {
                $requires = array_merge ($requires, array_map (function ($requirement)
                {
                    $requirement = $this->getPackageBlocks ($requirement);

                    return $requirement['full_path'];
                }, $this->packages[$package]->requires));

                $requires[] = $package;
            }
        }

        return array_unique ($requires);
    }

    /**
     * Получение информации из названия пакета
     * 
     * @param string $package - полное имя пакета
     */
    public function getPackageBlocks ($package)
    {
        $info   = explode (':', $package);
        $source = 'github';

        if (sizeof ($info) == 2)
            $source = strtolower ($info[0]);

        $info = explode ('/', end ($info));

        return array
        (
            'source'    => $source,
            'author'    => $info[0],
            'name'      => isset ($info[1]) ? $info[1] : '',
            'version'   => null,
            'full_name' => implode ('/', $info),
            'full_path' => $source .':'. implode ('/', $info)
        );
    }
}
