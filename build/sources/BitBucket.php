<?php

namespace Qero\Sources;

use Qero\Requester;

class BitBucket implements Source
{
    static $watermark = 'hash';
    public $package;

    public function __construct ($package, $version = null)
    {
        $this->package = $package;
    }

    public function getPackageCommit ()
    {
        $commits = json_decode (@Requester::getRequest ('https://api.bitbucket.org/2.0/repositories/'. $this->package .'/commits'), true);

        return isset ($commits['values'][0]) ?
            $commits['values'][0] : false;
    }

    public function getPackageArchive ()
    {
        return @Requester::getRequest ('https://bitbucket.org/'. $this->package .'/get/master.tar.gz', true);
    }
}
