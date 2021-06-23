<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Exception;

use InvalidArgumentException as PHPInvalidArgumentException;

class InvalidArgumentException extends PHPInvalidArgumentException implements ExceptionInterface
{
}
