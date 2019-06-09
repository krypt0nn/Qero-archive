<?php

namespace Qero\Sources;

use Qero\Requester;

class GitHub implements Source
{
    static $watermark = 'sha';
    public $package;

    public function __construct ($package, $version = null)
    {
        $this->package = $package;
    }

    public function getPackageCommit ()
    {
        $commits = json_decode (@Requester::getRequest ('https://api.github.com/repos/'. $this->package .'/commits'), true);

        return isset ($commits[0]) ?
            $commits[0] : false;
    }

    public function getPackageArchive ()
    {
        return @Requester::getRequest ('https://api.github.com/repos/'. $this->package .'/tarball', true);
    }
}
