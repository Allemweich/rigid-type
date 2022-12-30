<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Types\Address;
use Tests\Types\Credentials;
use Tests\Types\IncompleteInputAllowed;
use Tests\Types\IncompleteInputNotAllowed;
use Tests\Types\Invoice;

class RigidTypeTest extends TestCase
{
    public function testInstanceHasExpectedFieldsAndValues(): void
    {
        $input = [
            'amount'      => 25,
            'description' => 'your purchase from MyCorp',
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

        $this->assertSame($input['amount'], $invoice->amount);
        $this->assertNull($invoice->description);
        $this->assertNull($invoice->article);
        $this->assertNull($invoice->address);
        $this->assertNull($invoice->untyped);
    }

    public function testThrowExceptionOnIncompleteInputWhenStrict(): void
    {
        $this->expectExceptionMessage('IncompleteInputNotAllowed requires additional fields: id, name');

        new IncompleteInputNotAllowed([]);
    }

    public function testDoNotThrowExceptionOnIncompleteInputWhenNotStrict(): void
    {
        $input = ['id' => 18];

        $type = new IncompleteInputAllowed($input);

        $this->assertEquals(18, $type->id);
        $this->assertNull($type->name);
    }

    public function testThrowExceptionForNonNullableTypeOnMissingInputEvenWhenNotStrict(): void
    {
        $input = ['password' => 'password123'];

        $this->expectExceptionMessage('Credentials::$username');
        $this->expectExceptionMessage('string');
        $this->expectExceptionMessage('null');

        new Credentials($input);
    }

    public function testThrowsExceptionOnWrongMemberType(): void
    {
        $input = [
            'username' => 'first.last',
            'password' => (object)[],
        ];

        $this->expectExceptionMessage('Credentials::$password');
        $this->expectExceptionMessage('string');
        $this->expectExceptionMessage('stdClass');

        new Credentials($input);
    }

    public function testThrowExceptionWhenCreatingMemberOfRigidTypeWithWrongPropertyType(): void
    {
        $input = [
            'amount'      => 25,
            'address'     => (object)[
                'street' => 16,
                'number' => 'Schönweg',
            ],
        ];

        $this->expectExceptionMessage('Address::$number');
        $this->expectExceptionMessage('int');
        $this->expectExceptionMessage('string');

        new Invoice($input);
    }

    public function unacceptableInputs(): array
    {
        return [
            [
                0
            ],
            [
                1
            ],
            [
                'value1'
            ],
            [
                ['value1', 'value2']
            ],
        ];
    }

    /** @dataProvider unacceptableInputs */
    public function testRejectUnacceptableInput($input): void
    {
        $this->expectExceptionMessage('Input must be associative array or object');

        new Credentials($input);
    }

    public function testAssociativeArrayAndObjectInputLeadToSameResult(): void
    {
        $input = [
            'username' => 'user',
            'password' => 'pass',
        ];

        $this->assertEquals(
            new Credentials($input),
            new Credentials((object)$input)
        );
    }
}
