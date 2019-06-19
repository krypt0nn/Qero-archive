<?php

namespace Qero\Sources;

use Qero\Requester;
use Qero\Exception;

class GitLab implements Source
{
    static $watermark = 'id';
    public $package;
    public $package_id;

    public function __construct ($package, $version = null)
    {
        $this->package = $package;

        $package  = explode ('/', $package);
        $packages = json_decode (@Requester::getRequest ('https://gitlab.com/api/v4/users/'. $package[0] .'/projects/?search='. $package[1]), true);

        if (!is_array ($packages))
            throw new Exception ('Package '. $this->package .' not founded');

        $package = implode ('/', $package);

        foreach ($packages as $packageInfo)
            if ($packageInfo['path_with_namespace'] == $package)
            {
                $this->package_id = $packageInfo['id'];

                break;
            }

        if ($this->package_id === null)
            throw new Exception ('Package '. $this->package .' not founded');
    }

    public function getPackageCommit ()
    {
        $commits = json_decode (@Requester::getRequest ('https://gitlab.com/api/v4/projects/'. $this->package_id .'/repository/commits'), true);

        return isset ($commits[0]) ?
            $commits[0] : false;
    }

    public function getPackageArchive ()
    {
        return @Requester::getRequest ('https://gitlab.com/api/v4/projects/'. $this->package_id .'/repository/archive.tar', true);
    }
}
