<?php

namespace LaravelSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Fluent;

class MultiPolygon extends Type
{
    const MULTIPOLYGON = 'multipolygon';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return \DB::connection()->getSchemaGrammar()->typeMultipolygon(new Fluent);
    }

    public function getName()
    {
        return self::MULTIPOLYGON;
    }
}
