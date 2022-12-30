<?php

namespace Allemweich\RigidType;

interface BatchCasterInterface
{
    public function cast(string $class, array $items): array;
}
