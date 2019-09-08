<?php

namespace Qero;

use Qero\Sources\Source;

class Package
{
    public $name;
    public $full_name;
    public $source;
    public $version;
    public $requires;
    public $entry_point;
    public $after_install;
    public $scripts;
    public $watermark;

    protected $commit;

    protected $enteringPoints = array
    (
        'qero-init.php',
        'qero-main.php',
        'main.php',
        'index.php',
        'autorun.php',
        'startup.php'
    );

    public function __construct ($import = array ())
    {
        foreach ($import as $name => $value)
            $this->$name = $value;
    }

    public function download ($folder = null)
    {
        if ($folder === null)
            $folder = QERO_DIR .'/qero-packages';

        dir_delete ($folder .'/'. $this->name);

        if (!is_dir ($branch = dirname ($folder .'/'. $this->name)))
            mkdir ($branch, 0777, true);

        file_put_contents ($branch .'/branch.tar', $this->source->getPackageArchive ());

        Printer::say ('  Unpacking...');

        $archive = new \PharData ($branch .'/branch.tar');
        $archive->extractTo ($branch, null, true);

        rename ($branch .'/'. $archive->current ()->getFilename (), $folder .'/'. $this->name);

        unset ($archive);
        \PharData::unlinkArchive ($branch .'/branch.tar');

        return $this;
    }

    public function register ($folder = null)
    {
        if ($folder === null)
            $folder = QERO_DIR .'/qero-packages';

        $source = $this->source;
        $this->watermark = $this->commit[$source::$watermark];

        if (file_exists ($folder .'/'. $this->name .'/qero-info.json'))
            foreach (json_decode (file_get_contents ($folder .'/'. $this->name .'/qero-info.json'), true) as $name => $value)
                $this->$name = $value;

        if ($this->entry_point === null)
        {
            $name = explode ('/', $this->name);
            $name = end ($name);
            
            foreach (array_merge (array ($name .'.php'), $this->enteringPoints) as $entryPoint)
                if (file_exists ($folder .'/'. $this->name .'/'. $entryPoint))
                {
                    $this->entry_point = $entryPoint;

                    break;
                }
        }

        $packages = file_exists ($folder .'/packages.json') ?
            json_decode (file_get_contents ($folder .'/packages.json'), true) : array ();

        $packages[$this->full_name] = $this->export ();

        file_put_contents ($folder .'/packages.json', json_encode ($packages, defined ('JSON_PRETTY_PRINT') ?
            JSON_PRETTY_PRINT : 0));

        return $this;
    }

    public function remove ($folder = null)
    {
        if ($folder === null)
            $folder = QERO_DIR .'/qero-packages';

        dir_delete ($folder .'/'. $this->name);

        if (sizeof (scandir (dirname ($folder .'/'. $this->name))) <= 2)
            dir_delete (dirname ($folder .'/'. $this->name));

        $packages = file_exists ($folder .'/packages.json') ?
            json_decode (file_get_contents ($folder .'/packages.json'), true) : array ();

        unset ($packages[$this->full_name]);

        file_put_contents ($folder .'/packages.json', json_encode ($packages, defined ('JSON_PRETTY_PRINT') ?
            JSON_PRETTY_PRINT : 0));

        return $this;
    }

    public function getCommit ()
    {
        if ($this->commit === null)
            $this->commit = $this->source->getPackageCommit ();

        return $this->commit;
    }

    public function export ()
    {
        $export = get_object_vars ($this);

        foreach (array (
            'commit',
            'enteringPoints',
            'source'
        ) as $item)
            unset ($export[$item]);

        return array_filter ($export, function ($item)
        {
            return $item !== null;
        });
    }
}
