<?php

namespace Qero;

/**
 * Рекурсивное удаление директории и всех последующих директорий и файлов
 * 
 * @param string $path - директория для удаления
 */
function dir_delete ($path)
{
    if (!is_dir ($path))
        return false;

    foreach (array_slice (scandir ($path), 2) as $file)
        if (is_dir ($file = $path .'/'. $file))
        {
            dir_delete ($file);

            if (is_dir ($file))
                rmdir ($file);
        }

        else unlink ($file);

    rmdir ($path);

    return true;
}
