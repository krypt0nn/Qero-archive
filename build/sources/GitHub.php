<?php

namespace Qero\Sources\GitHub;
use Qero\Sources\Source;
use Qero\Requester\Requester;

class GitHub implements Source
{
    static $watermark = 'sha';

    public static function getPackageInfo ($package)
    {
        return json_decode (@Requester::getRequest ('https://api.github.com/repos/'. $package), true);
    }

    public static function getPackageCommit ($package)
    {
        $commit = json_decode (@Requester::getRequest ('https://api.github.com/repos/'. $package .'/commits'), true);

        return $commit[0];
    }

    public static function getPackageArchive ($package)
    {
        return @Requester::getRequest ('https://api.github.com/repos/'. $package .'/tarball');
    }
}

?>
