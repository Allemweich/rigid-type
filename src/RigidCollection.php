<?php

namespace Allemweich\RigidType;

use Illuminate\Support\Collection;

/**
 * Extend this class if you want to create specific collection where you can add typehints.
 *
 * Alternatively, you can use @see BatchCaster directly for the input validation and pass the result
 * into a default collection or other structure of your choice.
 */
abstract class RigidCollection extends Collection implements RigidCollectionInterface
{
    public function __construct(array $items = [])
    {
        $casted = $this->getCaster()->cast($this->targetClass(), $items);

        parent::__construct($casted);
    }

    public abstract function targetClass(): string;

    public function getCaster(): BatchCasterInterface
    {
        return new BatchCaster();
    }

    /** convert to generic collection to disable rigid type check, as item type WILL change */
    public function pluck($value, $key = null): Collection
    {
        return collect($this)->pluck($value, $key);
    }

    /** convert to generic collection to disable rigid type check, as item type MAY change */
    public function map(callable $callback): Collection
    {
        return collect($this)->map($callback);
    }
}
