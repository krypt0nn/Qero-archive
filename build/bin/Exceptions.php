<?php

namespace Qero\Exceptions;
use Qero\Printer\Printer;

class Exception extends \Exception {}

set_exception_handler (function ($exception)
{
    Printer::print ($exception->getMessage (), 2);
});

?>
