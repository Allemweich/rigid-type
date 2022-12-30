<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Types\IgnoresUnknownValues;
use Tests\Types\IncludesUnknownValues;

class UnknownValuesTest extends TestCase
{
    public function testIncludeUnknownValues(): void
    {
        $data = [
            'id' => 123,
            'unknown' => (object) [
                'name' => 'Anon',
                'age'  => 23,
            ]
        ];

        $obj = new IncludesUnknownValues($data);

        $this->assertEquals(123, $obj->id);
        $this->assertEquals('Anon', $obj->unknown->name);
        $this->assertEquals(23, $obj->unknown->age);
    }

    public function testIgnoreUnknownValues(): void
    {
        $data = [
            'id' => 123,
            'unknown' => (object) [
                'name' => 'Anon',
                'age'  => 23,
            ]
        ];

        $type = new IgnoresUnknownValues($data);

        $this->assertEquals(123, $type->id);
        $this->assertTrue(!isset($type->unknown));
    }
}
