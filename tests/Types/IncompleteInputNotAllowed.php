<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class IncompleteInputNotAllowed extends RigidType
{
    protected bool $allowIncompleteInput = false;

    public ?string $id;
    public ?string $name;
}
