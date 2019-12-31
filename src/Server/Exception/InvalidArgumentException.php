<?php

namespace Laminas\ApiTools\Doctrine\Server\Exception;

use InvalidArgumentExcpetion as PHPInvalidArgumentException;

/**
 * Class InvalidArgumentException
 *
 * @package Laminas\ApiTools\Doctrine\Server\Exception
 */
class InvalidArgumentException extends PHPInvalidArgumentException implements ExceptionInterface
{
}
