<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class ComplexType extends RigidType
{
    public string                $id;
    public SimpleType            $simpleType;
    public ?SimpleTypeCollection $simpleTypeCollection;
    public ?ComplexType          $complexType;
}
