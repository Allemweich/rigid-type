<?php

namespace Tests\Entities;

use EasybellLibs\RigidType\RigidType;

class Address extends RigidType
{
    public string $street;
    public int    $number;
}
