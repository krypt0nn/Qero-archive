<?php

$phar = new Phar ('Qero.phar');
$phar->buildFromDirectory ('build');

/*if (Phar::canCompress (Phar::GZ))
   $phar->compress (Phar::GZ, '.phar.gz');

elseif (Phar::canCompress (Phar::BZ2))
   $phar->compress (Phar::BZ2, '.phar.bz2');*/

?>
