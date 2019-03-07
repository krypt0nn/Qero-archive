<?php

$begin = microtime (true);

$phar = new Phar ('Qero.phar');
$phar->buildFromDirectory ('build');

fwrite (STDOUT, '

Builded for '. round (microtime (true) - $begin, 4) .' sec.
File size: '. round (filesize ('Qero.phar') / 1024, 2) .' Kb
PHP version: '. phpversion () .'
Date: '. date ('Y/m/d H:i:s') .' (timestamp '. time () .')

');

/*if (Phar::canCompress (Phar::GZ))
   $phar->compress (Phar::GZ, '.phar.gz');

elseif (Phar::canCompress (Phar::BZ2))
   $phar->compress (Phar::BZ2, '.phar.bz2');*/

?>
