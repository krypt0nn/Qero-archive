<?php

ini_set ('phar.readonly', 0);
date_default_timezone_set ('UTC');

$begin = microtime (true);

(new Phar ('Qero.phar'))->buildFromDirectory ('build');

fwrite (STDOUT, '

   Qero build complited!

   Builded for '. round (microtime (true) - $begin, 4) .' sec.
   File size: '. round (filesize ('Qero.phar') / 1024, 2) .' Kb
   PHP version: '. phpversion () .'
   Date: '. date ('Y/m/d H:i:s') .' (UTC, timestamp: '. time () .')

   Checksums:
      SHA1: '. strtoupper (sha1_file ('Qero.phar')) .'
      MD5: '. strtoupper (md5_file ('Qero.phar')) .'
      CRC32: '. crc32 (file_get_contents ('Qero.phar')) .'

');
