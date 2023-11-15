<?php

declare(strict_types=1);

namespace Uro\TeltonikaFmParser\Exception;

use Exception;

class CrcMismatchException extends Exception
{
    public function __construct()
    {
        parent::__construct('Provided CRC is different than calculated CRC');
    }
}
