<?php

namespace Laminas\ApiTools\Doctrine\Server\Exception;

use InvalidArgumentException as PHPInvalidArgumentException;

/**
 * Class InvalidArgumentException
 *
 * @package Laminas\ApiTools\Doctrine\Server\Exception
 */
class InvalidArgumentException extends PHPInvalidArgumentException implements ExceptionInterface
{
}
