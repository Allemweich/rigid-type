<?php

namespace Tests\Unit;

use EasybellLibs\RigidType\RigidType;
use PHPUnit\Framework\TestCase;
use stdClass;

class Credentials extends RigidType
{
    protected bool $forceCompleteInput = false;

    public string  $username;
    public ?string $password;
}

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
    public function testInstanceHasExpectedFieldsAndValues(): void
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

        $this->assertSame($input['amount'], $invoice->amount);
        $this->assertNull($invoice->description);
        $this->assertNull($invoice->article);
        $this->assertNull($invoice->address);
        $this->assertNull($invoice->untyped);
    }

    public function testThrowExceptionOnIncompleteInputWhenStrict(): void
    {
        $input = ['amount' => 25];

        $this->expectExceptionMessage('Invoice requires additional fields: description, article, address, untyped');

        new Invoice($input);
    }

    public function testDoNotThrowExceptionOnIncompleteInputWhenNotStrict(): void
    {
        $input = ['username' => 'first.last'];

        $credentials = new Credentials($input);

        $this->assertSame($input['username'], $credentials->username);
        $this->assertNull($credentials->password);
    }

    public function testThrowExceptionForNonNullableTypeOnMissingInputEvenWhenNotStrict(): void
    {
        $input = ['password' => 'password123'];

        $this->expectExceptionMessage('Credentials::$username must be string, null used');

        new Credentials($input);
    }

    public function testThrowsExceptionOnWrongMemberType(): void
    {
        $input = [
            'username' => 'first.last',
            'password' => (object)[],
        ];

        $this->expectExceptionMessage('Credentials::$password must be string or null, stdClass used');

        new Credentials($input);
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

    public function invalidInput(): array
    {
        return [
            [null],
            [[]],
            ['first.last', 'password123']
        ];
    }

    /** @dataProvider invalidInput */
    public function testRejectInvalidInput($input): void
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
