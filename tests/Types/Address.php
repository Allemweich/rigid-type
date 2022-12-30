<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class Address extends RigidType
{
    protected bool $allowIncompleteInput = false;
    protected bool $includeUnknownValues = true;

    public string $street;
    public int    $number;
}
