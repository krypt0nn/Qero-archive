<?php

namespace Qero\Sources;

interface Source
{
    /**
     * Конструктор
     * 
     * @param string $package - полное название пакета
     */
    public function __construct ($package, $version = null);

    /**
     * Получение последнего коммита пакета
     */
    public function getPackageCommit ();

    /**
     * Получение архива проекта
     */
    public function getPackageArchive ();
}
