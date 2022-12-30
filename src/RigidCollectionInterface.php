<?php

namespace Allemweich\RigidType;

interface RigidCollectionInterface
{
    public function targetClass(): string;
    public function getCaster(): BatchCasterInterface;
}
