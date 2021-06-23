<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

use function strrev;

class RevType extends Type
{
    const NAME = 'rev';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getGuidTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (! $value) {
            return null;
        }

        return strrev($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (! $value) {
            return null;
        }

        return strrev($value);
    }

    public function getName()
    {
        return static::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
