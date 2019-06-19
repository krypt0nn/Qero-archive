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

    public $basefolder;
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
        mkdir ($folder .'/'. $this->name, 0777, true);

        file_put_contents ($folder .'/'. $this->name .'/branch.tar', $this->source->getPackageArchive ());

        Printer::say ('  Unpacking...');

        $archive = new \PharData ($folder .'/'. $this->name .'/branch.tar');
        $archive->extractTo ($folder .'/'. $this->name, null, true);

        unset ($archive);
        \PharData::unlinkArchive ($folder .'/'. $this->name .'/branch.tar');

        return $this;
    }

    public function register ($folder = null)
    {
        if ($folder === null)
            $folder = QERO_DIR .'/qero-packages';

        $this->watermark = $this->commit[$this->source::$watermark];

        if (is_dir ($folder .'/'. $this->name))
            foreach (array_slice (scandir ($folder .'/'. $this->name), 2) as $dir)
                if (is_dir ($folder .'/'. $this->name .'/'. $dir))
                {
                    $this->basefolder = $dir;

                    break;
                }
        
        if (!is_dir ($folder .'/'. $this->name .'/'. $this->basefolder))
            $this->basefolder = str_replace ('/', '-', $this->name) .'-'. substr ($this->commit[$this->source::$watermark], 0, 7);

        if (file_exists ($folder .'/'. $this->name .'/'. $this->basefolder .'/qero-info.json'))
            foreach (json_decode (file_get_contents ($folder .'/'. $this->name .'/'. $this->basefolder .'/qero-info.json'), true) as $name => $value)
                $this->$name = $value;

        if ($this->entry_point === null)
        {
            $name = explode ('/', $this->name);
            $name = end ($name);
            
            foreach (array_merge (array ($name .'.php'), $this->enteringPoints) as $entryPoint)
                if (file_exists ($folder .'/'. $this->name .'/'. $this->basefolder .'/'. $entryPoint))
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
