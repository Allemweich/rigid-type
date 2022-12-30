<?php

namespace Tests\Unit;

use Allemweich\RigidType\BatchCaster;
use PHPUnit\Framework\TestCase;
use Tests\Types\Credentials;

class BatchCasterTest extends TestCase
{
    public function testItemsAreCasted(): void
    {
        $data = [
            ['username' => 'user-1', 'password' => 'pass-1'],
            ['username' => 'user-2', 'password' => 'pass-2']
        ];

        $items = (new BatchCaster())->cast(Credentials::class, $data);

        $this->assertTrue($items[0] instanceof Credentials);
        $this->assertEquals('user-1', $items[0]->username);
    }

    public function testGenericCollectionHasCastedItems(): void
    {
        $data = [
            ['username' => 'user-1', 'password' => 'pass-1'],
            ['username' => 'user-2', 'password' => 'pass-2']
        ];

        $casted = (new BatchCaster())->cast(Credentials::class, $data);

        $credentials = collect($casted);

        $this->assertTrue($credentials->first() instanceof Credentials);
        $this->assertEquals('user-1', $credentials->first()->username);
    }
}
