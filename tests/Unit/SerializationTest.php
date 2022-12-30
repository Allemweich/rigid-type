<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Types\ComplexType;

class SerializationTest extends TestCase
{
    public function testObjectFromSerializationEqualsObject(): void
    {
        $data = [
            'id'                   => 'complex-1',
            'simpleType'           => (object)['id' => 'simple-1'],
            'simpleTypeCollection' => [
                (object)['id' => 'simple-2'],
                (object)['id' => 'simple-3'],
            ],
            'complexType'          => [
                'id'                   => 'complex-2',
                'simpleType'           => (object)['id' => 'simple-4'],
                'simpleTypeCollection' => [
                    (object)['id' => 'simple-5'],
                    (object)['id' => 'simple-6'],
                ],
                'complexType'          => null,
            ],
            'unexpected-key' => 'surprise'
        ];

        $type1 = new ComplexType($data);
        $type2 = new ComplexType(json_decode($type1->toJson()));
        $type3 = new ComplexType(json_decode($type2->toJson(), true));

        $this->assertEquals('simple-5', $type3->complexType->simpleTypeCollection->first()->id);
        $this->assertEquals('surprise', $type3->{'unexpected-key'});
        $this->assertEquals($type1, $type2);
        $this->assertEquals($type2, $type3);
    }
}
