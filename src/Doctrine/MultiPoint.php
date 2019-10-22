<?php

namespace LaravelSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Fluent;

class MultiPoint extends Type
{
    const MULTIPOINT = 'multipoint';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return \DB::connection()->getSchemaGrammar()->typeMultipoint(new Fluent);
    }

    public function getName()
    {
        return self::MULTIPOINT;
    }
}
