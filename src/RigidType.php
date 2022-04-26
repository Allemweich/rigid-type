<?php

namespace EasybellLibs\RigidType;

use EasybellLibs\RigidType\Exceptions\TypeValidationException;
use ReflectionClass;
use ReflectionProperty;
use TypeError;

abstract class RigidType
{
    /**
     * @var bool $forceCompleteInput
     * Defaults to true, can be set per subclass.
     * If the flag is true, incomplete inputs will be rejected.
     * If the flag is false, missing fields will be set to null, if their type allows it.
     */
    protected bool $forceCompleteInput = true;

    /**
     * @throws TypeValidationException
     */
    public function __construct($genericData)
    {
        $input = $this->getInputObject($genericData);

        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        if ($this->forceCompleteInput) {
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
            /** @var ReflectionProperty $property */
            $fieldName = $property->getName();
            $fieldType = ($property->getType()) ? $property->getType()->getName() : null;

            $value = $input->{$fieldName} ?? null;

            if ($value !== null && is_a($fieldType, RigidType::class, true)) {
                $value = new $fieldType($value);
            }

            $this->{$fieldName} = $value;
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

