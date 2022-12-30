<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidCollection;

/**
 * @method Address first(callable $callback = null, $default = null)
 * @method Address get($key, $default = null)
 */
class AddressCollection extends RigidCollection
{
    public function targetClass(): string
    {
        return Address::class;
    }
}
