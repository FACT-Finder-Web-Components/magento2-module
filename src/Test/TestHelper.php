<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test;

use ReflectionClass;

class TestHelper
{
    public static function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
