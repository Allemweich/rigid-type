<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class SimpleType extends RigidType
{
    public static string $problem1 = 'do-not-serialize';
    protected string $problem2 = 'do-not-serialize';
    private string $problem3 = 'do-not-serialize';

    public string $id;
}
