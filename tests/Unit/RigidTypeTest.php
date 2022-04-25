<?php

namespace Tests\Unit;

use EasybellLibs\RigidType\RigidType;
use PHPUnit\Framework\TestCase;
use stdClass;

class Address extends RigidType
{
    public string $street;
    public int    $number;
}

class Invoice extends RigidType
{
    public int      $amount;
    public ?string  $description;
    public ?object  $article;
    public ?Address $address;
    public          $untyped;
}

class RigidTypeTest extends TestCase
{
    public function testHasExpectedFields(): void
    {
        $input = [
            'amount'      => 25,
            'description' => 'your purchase from easybell',
            'article'     => (object)['number' => 453],
            'address'     => (object)[
                'street' => 'Schönweg',
                'number' => 16,
            ],
            'untyped'     => 'sometimes I am a string'
        ];

        $invoice = new Invoice($input);

        $this->assertTrue($invoice->article instanceof stdClass);
        $this->assertTrue($invoice->address instanceof Address);

        $this->assertSame($input['amount'],          $invoice->amount);
        $this->assertSame($input['description'],     $invoice->description);
        $this->assertSame($input['article']->number, $invoice->article->number);
        $this->assertSame($input['address']->street, $invoice->address->street);
        $this->assertSame($input['address']->number, $invoice->address->number);
        $this->assertSame($input['untyped'],         $invoice->untyped);
    }

    public function testRespectNullableTypes(): void
    {
        $input = [
            'amount'      => 25,
            'description' => null,
            'article'     => null,
            'address'     => null,
            'untyped'     => null,
        ];

        $invoice = new Invoice($input);

        $this->assertEquals(25, $invoice->amount);
        $this->assertNull($invoice->address);
    }

    public function testThrowExceptionOnIncompleteInput(): void
    {
        $input = ['amount' => 25];

        $this->expectExceptionMessage('Invoice requires additional fields: description, article, address, untyped');

        new Invoice($input);
    }

    public function testThrowsExceptionOnWrongMemberType(): void
    {
        $input = [
            'amount'      => 25,
            'description' => 'your purchase from easybell',
            'article'     => 453,
            'address'     => (object)[
                'street' => 'Schönweg',
                'number' => 16,
            ],
            'untyped'     => 'sometimes I am a string'
        ];

        $this->expectExceptionMessage('Invoice::$article must be object or null, int used');

        new Invoice($input);
    }

    public function testThrowExceptionWhenCreatingMemberOfRigidTypeWithWrongPropertyType(): void
    {
        $input = [
            'amount'      => 25,
            'description' => null,
            'article'     => null,
            'address'     => (object)[
                'street' => 16,
                'number' => 'Schönweg',
            ],
            'untyped'     => null,
        ];

        $this->expectExceptionMessage('Address::$number must be int, string used');

        new Invoice($input);
    }
}
