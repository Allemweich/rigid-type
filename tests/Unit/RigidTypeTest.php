<?php

namespace Tests\Unit;

use EasybellLibs\RigidType\RigidType;
use PHPUnit\Framework\TestCase;

class Address extends RigidType
{
    public string $street;
    public int    $number;
}

class Invoice extends RigidType
{
    public          $uuid;
    public int      $amount;
    public ?string  $description;
    public ?object  $article;
    public ?Address $address;
}

class RigidTypeTest extends TestCase
{
    public function testHasExpectedFields(): void
    {
        $input = [
            'uuid'        => 123,
            'amount'      => 25,
            'description' => 'your purchase from easybell',
            'article'     => (object)['number' => 453],
            'address'     => (object)[
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

    public function testRespectNullableTypes(): void
    {
        $input = [
            'uuid'        => 123,
            'amount'      => 25,
            'description' => null,
            'article'     => null,
            'address'     => null,
        ];

        $invoice = new Invoice($input);

        $this->assertEquals(25, $invoice->amount);
        $this->assertNull($invoice->address);
    }

    public function testThrowExceptionOnIncompleteInput(): void
    {
        $input = ['uuid' => 123, 'amount' => 25];

        $this->expectExceptionMessage('Invoice requires additional fields: description, article, address');

        new Invoice($input);
    }

    public function testThrowsExceptionOnWrongMemberType(): void
    {
        $input = [
            'uuid'        => 123,
            'amount'      => 25,
            'description' => 'your purchase from easybell',
            'article'     => 453,
            'address'     => (object)[
                'street' => 'Schönweg',
                'number' => 16,
            ],
        ];

        $this->expectExceptionMessage('Invoice::$article must be object or null, int used');

        new Invoice($input);
    }

    public function testThrowExceptionWhenCreatingMemberOfRigidTypeWithWrongPropertyType(): void
    {
        $input = [
            'uuid'        => 123,
            'amount'      => 25,
            'description' => 'your purchase from easybell',
            'article'     => (object)[],
            'address'     => (object)[
                'street' => 16,
                'number' => 'Schönweg',
            ],
        ];

        $this->expectExceptionMessage('Address::$number must be int, string used');

        new Invoice($input);
    }
}
