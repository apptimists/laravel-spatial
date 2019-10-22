<?php

namespace LaravelSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Fluent;

class LineString extends Type
{
    const LINESTRING = 'linestring';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return \DB::connection()->getSchemaGrammar()->typeLinestring(new Fluent);
    }

    public function getName()
    {
        return self::LINESTRING;
    }
}
