<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Types\Address;
use Tests\Types\AddressCollection;

class RigidCollectionTest extends TestCase
{
    public function testCollectionCanBeFilteredAndPlucked(): void
    {
        $data = [
            ['street' => 'street', 'number' => 1],
            ['street' => 'path',   'number' => 2],
            ['street' => 'way',    'number' => 3],
        ];

        $addresses = new AddressCollection($data);

        $this->assertTrue($addresses->first() instanceof Address);

        $filtered = $addresses->filter(fn(Address $item) => $item->number > 1);
        $plucked = $filtered->pluck('street');

        $this->assertEquals(['path', 'way'], $plucked->toArray());
    }

    public function testCollectionCanBeMapped(): void
    {
        $data = [
            ['street' => 'alley',  'number' => 1],
            ['street' => 'path',   'number' => 2],
            ['street' => 'way',    'number' => 3],
        ];

        $addresses = new AddressCollection($data);

        $addresses = $addresses->map(fn(Address $address) => $address->street);

        $this->assertEquals('alley', $addresses->first());
    }

    public function testFailIfOneItemIsInvalid(): void
    {
        $data = [
            ['street' => 'alley',  'number' => 1],
            ['street' => 'path',   'number' => 2],
            ['street' => 'way'],
        ];

        $this->expectExceptionMessage('Address requires additional fields: number","input":{"street":"way"}');
        new AddressCollection($data);
    }
}
