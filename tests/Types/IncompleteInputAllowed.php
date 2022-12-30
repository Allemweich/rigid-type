<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class IncompleteInputAllowed extends RigidType
{
    public ?string $id;
    public ?string $name;
}
