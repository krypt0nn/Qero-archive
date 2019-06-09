<?php

namespace Qero;

class Exception extends \Exception {}

set_exception_handler (function ($exception)
{
    Printer::say ($exception->getMessage (), 1);
});

