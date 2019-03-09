<?php

namespace Qero\Sources\GitLab;
use Qero\Sources\Source;
use Qero\Requester\Requester;

class GitLab implements Source
{
    static $watermark = 'id';
    static $cache     = array ();
    
    public static function getPackageInfo ($package)
    {
        $package  = explode ('/', $package);
        $packages = json_decode (@Requester::getRequest ('https://gitlab.com/api/v4/users/'. $package[0] .'/projects/?search='. $package[1]), true);

        if (!is_array ($packages))
            return false;

        $package = implode ('/', $package);

        foreach ($packages as $packageInfo)
            if ($packageInfo['path_with_namespace'] == $package)
            {
                self::$cache[$package] = $packageInfo['id'];

                return $packageInfo;
            }
    }

    public static function getPackageCommit ($package)
    {
        $commit = json_decode (@Requester::getRequest ('https://gitlab.com/api/v4/projects/'. self::$cache[$package] .'/repository/commits'), true);

        return $commit[0];
    }

    public static function getPackageArchive ($package)
    {
        return @Requester::getRequest ('https://gitlab.com/api/v4/projects/'. self::$cache[$package] .'/repository/archive.tar');
    }
}

?>
