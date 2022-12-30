<?php

namespace Allemweich\RigidType;

use Allemweich\RigidType\Exceptions\TypeValidationException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;
use TypeError;

abstract class RigidType implements Arrayable, Jsonable
{
    protected bool $allowIncompleteInput = true;
    protected bool $includeUnknownValues = true;

    /**
     * @throws TypeValidationException
     */
    public function __construct($input)
    {
        $input = $this->getSanitizedInput($input);

        $properties = $this->getPublicProperties();

        if (!$this->allowIncompleteInput) {
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

    public function toArray(): array
    {
        return $this->getUnprotectedValues($this);
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    private function ensureInputContainsAllProperties(array $properties, array $input): void
    {
        $inputFields = array_keys($input);
        $requiredFields = array_column($properties, 'name');
        $missingFields = array_diff($requiredFields, $inputFields);

        if(count($missingFields) > 0) {
            throw new TypeValidationException(json_encode([
                'error' => 'Entity ' . static::class. ' requires additional fields: ' . implode(', ', $missingFields),
                'input' => $input,
            ]));
        }
    }

    private function takeValues(array $properties, array $input): void
    {
        foreach ($properties as $property) {
            /** @var ReflectionProperty $property */
            $fieldName = $property->getName();
            $fieldType = ($property->getType()) ? $property->getType()->getName() : null;

            $value = $input[$fieldName] ?? null;

            if ($value !== null && $fieldType !== null && $this->supportsCasting($fieldType)) {
                $value = new $fieldType($value);
            }

            $this->{$fieldName} = $value;

            unset($input[$fieldName]);
        }

        if (!$this->includeUnknownValues) {
            return;
        }

        foreach ($input as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private function getSanitizedInput($input): array
    {
        if ($input === []) {
            return $input;
        }

        if (is_object($input)) {
            $input = $this->getUnprotectedValues($input);
        }

        if (is_array($input) && Arr::isAssoc($input) ) {
            return $input;
        }

        throw new TypeValidationException(json_encode([
            'error' => 'Input must be associative array or object',
            'input' => $input
        ]));
    }

    protected function supportsCasting(string $fieldType)
    {
        return is_a($fieldType, RigidType::class, true)
            || is_a($fieldType, RigidCollection::class, true);
    }

    private function getUnprotectedValues(object $input): array
    {
        return array_filter(
            (array) $input,
            fn ($key) => strpos($key, "\0") === false,
            ARRAY_FILTER_USE_KEY
        );
    }

    /** @return ReflectionProperty[] */
    private function getPublicProperties(): array
    {
        $reflection = new ReflectionClass($this);
        $public = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $static = $reflection->getProperties(ReflectionProperty::IS_STATIC);
        return array_diff($public, $static);
    }
}

