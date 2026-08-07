<?php

namespace App\Core;

class Validator
{
    /** @return array<string,string> Errores indexados por campo (vacio si es valido). */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $ruleString) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $error = self::applyRule($name, $param, $field, $value, $data);

                if ($error !== null) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }

        return $errors;
    }

    private static function applyRule(string $name, ?string $param, string $field, mixed $value, array $data): ?string
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        return match ($name) {
            'required' => ($value === null || trim((string) $value) === '') ? "$label es obligatorio." : null,
            'email' => ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) ? "$label debe ser un correo valido." : null,
            'numeric' => ($value !== null && $value !== '' && !is_numeric($value)) ? "$label debe ser numerico." : null,
            'min' => ($value !== null && $value !== '' && strlen((string) $value) < (int) $param) ? "$label debe tener al menos $param caracteres." : null,
            'max' => ($value !== null && $value !== '' && strlen((string) $value) > (int) $param) ? "$label no puede superar $param caracteres." : null,
            'date' => ($value && !strtotime((string) $value)) ? "$label debe ser una fecha valida." : null,
            'in' => ($value !== null && $value !== '' && !in_array($value, explode(',', (string) $param), true)) ? "$label no es valido." : null,
            'min_value' => ($value !== null && $value !== '' && (float) $value < (float) $param) ? "$label debe ser mayor o igual a $param." : null,
            default => null,
        };
    }
}
