<?php

namespace EasybellLibs\RigidType;

use EasybellLibs\RigidType\Exceptions\TypeValidationException;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;
use TypeError;

abstract class RigidType
{
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
        $inputFields = collect($input)->keys();
        $requiredFields = collect($properties)->pluck('name');

        $missingFields = $requiredFields->diff($inputFields);

        throw_if($missingFields->isNotEmpty(), new TypeValidationException(json_encode([
            'error' => 'Entity ' . __CLASS__ . ' requires additional fields: ' . $missingFields->join(', '),
            'input' => $input
        ])));
    }

    private function takeValues(array $properties, object $input): void
    {
        if (empty($input)) {
            return;
        }

        foreach ($properties as $property) {
            $value = data_get($input, $property->name);

            $type = $property->getType()->getName();

            if (is_a($type, RigidType::class, true)) {
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

        if (is_array($genericData) && Arr::isAssoc($genericData)) {
            return (object)$genericData;
        }

        throw new TypeValidationException(json_encode([
            'error' => 'Input must be associative array or object',
            'input' => $genericData
        ]));
    }
}

