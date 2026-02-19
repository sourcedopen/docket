<?php

namespace App\Services;

class CustomFieldValidator
{
    /**
     * Validates custom fields against a schema definition.
     *
     * @param  array<string, mixed>  $customFields
     * @param  array<int, array{key: string, label: string, type: string, required?: bool, options?: list<string>}>  $schemaDefinition
     * @return array<string, string> Validation errors keyed by field key
     */
    public function validate(array $customFields, array $schemaDefinition): array
    {
        $errors = [];

        foreach ($schemaDefinition as $field) {
            $key = $field['key'];
            $value = $customFields[$key] ?? null;
            $isEmpty = $value === null || $value === '';

            if (! empty($field['required']) && $isEmpty) {
                $errors[$key] = "The {$field['label']} field is required.";

                continue;
            }

            if ($isEmpty) {
                continue;
            }

            $type = $field['type'] ?? 'text';

            if ($type === 'number' && ! is_numeric($value)) {
                $errors[$key] = "The {$field['label']} must be a number.";
            } elseif ($type === 'date' && ! strtotime((string) $value)) {
                $errors[$key] = "The {$field['label']} must be a valid date.";
            } elseif ($type === 'select' && isset($field['options']) && ! in_array($value, $field['options'], true)) {
                $errors[$key] = "The selected {$field['label']} is invalid.";
            }
        }

        return $errors;
    }
}
