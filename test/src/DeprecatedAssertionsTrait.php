<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine;

use PHPUnit\Framework\Assert;
use ReflectionProperty;

trait DeprecatedAssertionsTrait
{
    /** @param mixed $expected */
    public static function assertAttributeEquals(
        $expected,
        string $property,
        object $instance,
        string $message = ''
    ): void {
        $r = new ReflectionProperty($instance, $property);
        $r->setAccessible(true);
        Assert::assertEquals($expected, $r->getValue($instance), $message);
    }

    /** @param mixed $expected */
    public static function assertAttributeSame(
        $expected,
        string $property,
        object $instance,
        string $message = ''
    ): void {
        $r = new ReflectionProperty($instance, $property);
        $r->setAccessible(true);
        Assert::assertSame($expected, $r->getValue($instance), $message);
    }
}
