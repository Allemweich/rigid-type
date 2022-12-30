<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidCollection;

/**
 * @method SimpleType first(callable $callback = null, $default = null)
 * @method SimpleType get(callable $callback = null, $default = null)
 */
class SimpleTypeCollection extends RigidCollection
{
    public function targetClass(): string
    {
        return SimpleType::class;
    }
}
