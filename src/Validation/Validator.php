<?php

namespace Core\Validation;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$ruleName, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

        if ($ruleName === 'required' && ($value === null || $value === '')) {
            $this->addError($field, 'Este campo é obrigatório.');
        }

        if ($ruleName === 'email' && $value !== null && $value !== '') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, 'Informe um e-mail válido.');
            }
        }

        if ($ruleName === 'min' && $value !== null && $value !== '') {
            if (mb_strlen((string) $value) < (int) $parameter) {
                $this->addError(
                    $field,
                    "Este campo deve ter no mínimo {$parameter} caracteres."
                );
            }
        }

        if ($ruleName === 'max' && $value !== null && $value !== '') {
            if (mb_strlen((string) $value) > (int) $parameter) {
                $this->addError(
                    $field,
                    "Este campo deve ter no máximo {$parameter} caracteres."
                );
            }
        }

        if ($ruleName === 'numeric' && $value !== null && $value !== '') {
            if (!is_numeric($value)) {
                $this->addError($field, 'Este campo deve ser numérico.');
            }
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}