<?php

namespace Qero\Sources;

interface Source
{
    /**
     * Получение информации о пакете
     * 
     * @param string $package - полное название пакета
     */
    public static function getPackageInfo ($package);
    
    /**
     * Получение последнего коммита пакета
     * 
     * @param string $package - полное название пакета
     */
    public static function getPackageCommit ($package);

    /**
     * Получение архива проекта
     * 
     * @param string $package - полное название пакета
     */
    public static function getPackageArchive ($package);
}
