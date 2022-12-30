<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class IgnoresUnknownValues extends RigidType
{
    protected bool $includeUnknownValues = false;

    public string $id;
}
