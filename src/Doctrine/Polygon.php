<?php

namespace LaravelSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Fluent;

class Polygon extends Type
{
    const POLYGON = 'polygon';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return \DB::connection()->getSchemaGrammar()->typePolygon(new Fluent);
    }

    public function getName()
    {
        return self::POLYGON;
    }
}
