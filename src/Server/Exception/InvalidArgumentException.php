<?php

namespace Laminas\ApiTools\Doctrine\Server\Exception;

use InvalidArgumentException as PHPInvalidArgumentException;

class InvalidArgumentException extends PHPInvalidArgumentException implements ExceptionInterface
{
}
