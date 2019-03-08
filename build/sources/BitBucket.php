<?php

namespace Qero\Sources\BitBucket;
use Qero\Sources\Source;
use Qero\Requester\Requester;

class BitBucket implements Source
{
    public static function getPackageInfo ($package)
    {
        return json_decode (@Requester::getRequest ('https://api.bitbucket.org/2.0/repositories/'. $package), true);
    }

    public static function getPackageCommit ($package)
    {
        $commit = json_decode (@Requester::getRequest ('https://api.bitbucket.org/2.0/repositories/'. $package .'/commits'), true);

        return $commit['values'][0];
    }

    public static function getPackageArchive ($package)
    {
        return @Requester::getRequest ('https://bitbucket.org/'. $package .'/get/master.tar.gz');
    }
}

?>
