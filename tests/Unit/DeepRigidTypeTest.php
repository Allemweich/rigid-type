<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Types\ComplexType;
use Tests\Types\SimpleType;
use Tests\Types\SimpleTypeCollection;

class DeepRigidTypeTest extends TestCase
{
    public function testCanCreateComplexRigidType(): void
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
        ];

        $complexType = new ComplexType($data);

        $this->assertTrue($complexType->simpleType instanceof SimpleType);
        $this->assertTrue($complexType->complexType instanceof ComplexType);
        $this->assertTrue($complexType->simpleTypeCollection instanceof SimpleTypeCollection);
        $this->assertTrue($complexType->complexType instanceof ComplexType);

        $this->assertEquals('simple-5', $complexType->complexType->simpleTypeCollection->first()->id);
    }
}
