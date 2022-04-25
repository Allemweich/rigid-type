<?php

namespace Tests\Entities;

use EasybellLibs\RigidType\RigidType;

class Invoice extends RigidType
{
    public int     $amount;
    public string  $description;
    public object  $article;
    public Address $address;
}
