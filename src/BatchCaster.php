<?php

namespace Allemweich\RigidType;

use RuntimeException;

class BatchCaster implements BatchCasterInterface
{
    public function cast(string $class, array $items): array
    {
        if (!class_exists($class) || !$this->supports($class)) {
            $message = __METHOD__ . ' does not support casting to ' . $class . '. Extend the supports() method if necessary';
            throw new RuntimeException($message);
        }

        return array_map(fn($item) => new $class($item), $items);
    }

    protected function supports(string $class): bool
    {
        return is_a($class, RigidType::class, true);
    }
}
