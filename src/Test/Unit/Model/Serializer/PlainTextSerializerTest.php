<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Serializer;

use PHPUnit\Framework\TestCase;

class PlainTextSerializerTest extends TestCase
{
    /** @var PlainTextSerializer */
    private $serializer;

    public function test_serialize_should_throw_an_exception()
    {
        $this->expectException('BadMethodCallException');
        $this->serializer->serialize(['key' => 'value']);
    }

    public function test_unserialize_should_return_array_with_true_if_value_contains_success()
    {
        $result = $this->serializer->unserialize('The event was successfully tracked');
        $this->assertSame(['success' => true], $result, "Result should be an associative array with 'success' key and value equal to 'true'");
    }

    public function test_unserialize_should_return_array_with_false_if_value_does_not_contain_success()
    {
        $result = $this->serializer->unserialize('Deduplicate field configured but no masterId provided');
        $this->assertSame(['success' => false], $result, "Result should be an associative array with 'success' key and value equal to 'false'");
    }

    protected function setUp(): void
    {
        $this->serializer = new PlainTextSerializer();
    }
}
