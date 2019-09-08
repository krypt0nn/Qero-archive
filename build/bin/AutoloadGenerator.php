<?php

namespace Qero;

use ProgressBar\ProgressBar;

class AutoloadGenerator
{
    public static function generateAutoload ()
    {
        global $controller;

        $autoload = '<?php'. QERO_AUTOGENERATE;
        $requires = '';
        $classes  = '';

        $packages = $controller->manager->getRequires (array_keys ($controller->manager->packages));

        if (($size = sizeof ($packages)) > 0)
            $progressBar = new ProgressBar ($size, 48, '   Building... ');

        $i = 0;

        foreach ($packages as $file)
        {
            if (!isset ($controller->manager->packages[$file]))
            {
                if (isset ($progressBar))
                    $progressBar->update (++$i);

                continue;
            }

            $baseFile = explode (':', $file);
            $baseFile = implode (':', array_slice ($baseFile, 1));

            if (!isset ($controller->manager->packages[$file]->entry_point))
                foreach (self::getPHPClasses (QERO_DIR .'/qero-packages/'. $baseFile) as $pathFile => $fileClasses)
                    foreach ($fileClasses as $class)
                        $classes .= "'$class' => '$baseFile/$pathFile',\n\t";

            else $requires .= 'require \''. $baseFile .'/'. $controller->manager->packages[$file]->entry_point ."';\n";

            if (isset ($progressBar))
                $progressBar->update (++$i);
        }

        if (isset ($progressBar))
            fwrite (STDOUT, PHP_EOL . PHP_EOL);

        $autoload .= $requires . strlen ($classes) > 3 ? '

$classes = array
(
    '. (substr ($classes, -3) == ",\n\t" ? substr ($classes, 0, -3) : $classes) .'
);

spl_autoload_register (function ($class) use ($classes)
{
    if (isset ($classes[$class]))
        include __DIR__ .\'/\'. $classes[$class];
});' : '';

        file_put_contents (QERO_DIR .'/qero-packages/autoload.php', $autoload ."\n\n\$required_packages = ". ($size > 0 ? "array\n(\n\tarray (". implode ("),\n\tarray (", array_map (function ($package) use ($controller)
        {
			if (isset ($controller->manager->packages[$package]))
				return "'$package', ". ($controller->manager->packages[$package]->version !== null ?
					'\''. $controller->manager->packages[$package]->version .'\'' : 'null');
        }, $packages)) .")\n);" : 'null;') ."\n");
    }

    public static function getPHPClasses ($file)
    {
        if (is_dir ($file))
        {
            global $controller;

            $classes = array ();

            foreach ($controller->manager->getPhpsList ($file) as $path)
                $classes[$path] = self::getPHPClasses ($file .'/'. $path);

            return $classes;
        }

        else
        {
            $contents = @php_strip_whitespace ($file);

            if (!preg_match ('{\b(?:class|interface'. (PHP_VERSION_ID < 50400 ? '' : '|trait') .')\s}i', $contents))
                return array ();

            $contents = preg_replace ('{<<<\s*(\'?)(\w+)\\1(?:\r\n|\n|\r)(?:.*?)(?:\r\n|\n|\r)\\2(?=\r\n|\n|\r|;)}s', 'null', $contents);
            $contents = preg_replace ('{"[^"\\\\]*+(\\\\.[^"\\\\]*+)*+"|\'[^\'\\\\]*+(\\\\.[^\'\\\\]*+)*+\'}s', 'null', $contents);

            if (substr ($contents, 0, 2) != '<?')
            {
                $contents = preg_replace ('{^.+?<\?}s', '<?', $contents, 1, $replacements);

                if ($replacements === 0)
                    return array ();
            }

            $contents = preg_replace ('{\?>.+<\?}s', '?><?', $contents);
            $pos      = strrpos ($contents, '?>');

            if ($pos !== false && strpos (substr ($contents, $pos), '<?') === false)
                $contents = substr ($contents, 0, $pos);

            if (preg_match ('{(<\?)(?!(php))}i', $contents))
                $contents = preg_replace ('{//.* | /\*(?:[^*]++|\*(?!/))*\*/}x', '', $contents);

            preg_match_all ('{(?:\b(?<![\$:>])(?P<type>class|interface'. (PHP_VERSION_ID < 50400 ? '' : '|trait') .') \s++ (?P<name>[a-zA-Z_\x7f-\xff:][a-zA-Z0-9_\x7f-\xff:\-]*+)| \b(?<![\$:>])(?P<ns>namespace) (?P<nsname>\s++[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\s*+\\\\\s*+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)*+)? \s*+ [\{;])}ix', $contents, $matches);

            $classes   = array ();
            $namespace = '';

            for ($i = 0, $len = sizeof ($matches['type']); $i < $len; $i++)
            {
                if (!empty ($matches['ns'][$i]))
                    $namespace = str_replace (array (
                        ' ', "\t", "\r", "\n"
                    ), '', $matches['nsname'][$i]) .'\\';
                
                else
                {
                    $name = $matches['name'][$i];

                    if ($name == 'extends' || $name == 'implements')
                        continue;

                    $classes[] = ltrim ($namespace . $name, '\\');
                }
            }

            return $classes;
        }
    }
}
