<?php

namespace EasybellLibs\RigidType;

use EasybellLibs\RigidType\Exceptions\TypeValidationException;
use ReflectionClass;
use ReflectionProperty;
use TypeError;

abstract class RigidType
{
    /**
     * @throws TypeValidationException
     */
    public function __construct($genericData, bool $checkCompleteness = true)
    {
        $input = $this->getInputObject($genericData);

        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        if ($checkCompleteness) {
            $this->ensureInputContainsAllProperties($properties, $input);
        }

        try {
            $this->takeValues($properties, $input);
        } catch (TypeError $exception) {
            throw new TypeValidationException(json_encode([
                'error' => $exception->getMessage(),
                'input' => $input
            ]));
        }
    }

    private function ensureInputContainsAllProperties(array $properties, object $input): void
    {
        $inputFields = array_keys(get_object_vars($input));
        $requiredFields = array_column($properties, 'name');
        $missingFields = array_diff($requiredFields, $inputFields);

        if(count($missingFields) > 0) {
            throw new TypeValidationException(json_encode([
                'error' => 'Entity ' . static::class. ' requires additional fields: ' . implode(', ', $missingFields),
                'input' => $input,
            ]));
        }
    }

    private function takeValues(array $properties, object $input): void
    {
        if (empty($input)) {
            return;
        }

        foreach ($properties as $property) {
            $value = $input->{$property->getName()} ?? null;

            $type = $property->getType()->getName();

            if (is_a($type, RigidType::class, true) && $value !== null) {
                $value = new $type($value);
            }

            $this->{$property->getName()} = $value;
        }
    }

    private function getInputObject($genericData): object
    {
        if (is_object($genericData)) {
            return $genericData;
        }

        if (is_array($genericData) && $this->isAssocArray($genericData)) {
            return (object)$genericData;
        }

        throw new TypeValidationException(json_encode([
            'error' => 'Input must be associative array or object',
            'input' => $genericData
        ]));
    }

    private function isAssocArray(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}

