<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use Tests\Entities\Address;
use Tests\Entities\Invoice;

class RigidTypeTest extends TestCase
{
    public function testHasExpectedFields(): void
    {
        $input = [
            'amount' => 25,
            'description' => 'your purchase from easybell',
            'article' => (object)['number' => 453],
            'address' => (object)[
                'street' => 'Schönweg',
                'number' => 16,
            ],
        ];

        $invoice = new Invoice($input);

        $this->assertTrue($invoice instanceof Invoice);
        $this->assertTrue($invoice->article instanceof \stdClass);
        $this->assertTrue($invoice->address instanceof Address);

        $this->assertEquals(25, $invoice->amount);
        $this->assertEquals('your purchase from easybell', $invoice->description);
        $this->assertEquals(453, $invoice->article->number);
        $this->assertEquals('Schönweg', $invoice->address->street);
        $this->assertEquals(16, $invoice->address->number);
    }

    public function testThrowsExceptionOnWrongMemberType(): void
    {

    }

    public function testMemberOfRigidTypeHasExpectedFields(): void
    {

    }

    public function testThrowExceptionWhenCreatingMemberOfRigidTypeWithWrongPropertyType(): void
    {

    }
}
