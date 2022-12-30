<?php

namespace Tests\Types;

use Allemweich\RigidType\RigidType;

class Invoice extends RigidType
{
    public int      $amount;
    public ?string  $description;
    public ?object  $article;
    public ?Address $address;
    public          $untyped;
}
