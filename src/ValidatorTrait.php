<?php

namespace Swing;

use Exception;
use UnexpectedValueException;

trait ValidatorTrait
{
    protected $messages = [
        'integer' => 'Value must be integer number',
        'string' => 'Text contains special characters',
        'min' => 'Minimum length of the text is not met',
        'max' => 'Text exceeds maximum length',
        'timestamp' => 'Date must follow format YYYY-MM-DD HH:MM:SS',
    ];

    protected $errors = [];

    /*
     * Conditional operations override the rest if their condition is met
     *
     * for example nullable will invalidate the failed integers, strings, etc.
     * it may be used for some operator other than nullable
     * like "sometimes" where key doesn't exist in Request
     *
     * in other words conditionals override the rest if they are true
     * if false they do nothing
     */
    protected $conditional = ['nullable'];

    public function __call($name, $arguments)
    {
        throw new UnexpectedValueException("Rule {$name} not supported in validator");
    }

    protected function checkRules($content, $rulesList): array
    {
        $result = [];

        foreach ($rulesList as $rule) {
            // manages with syntax 'max:3'
            list($rule, $value) = array_pad(explode(':', $rule, 2), 2, null);

            //call the validation rule
            $result[$rule] = $this->{$rule}($content, $value);
        }

        return $result;
    }

    /**
     * Validate Request or single variable
     *
     * @param mixed $content
     * @param array $rules
     */
    public function validate($content, array $rules): void
    {
        foreach ($rules as $key => $rule) {
            $checkContent = $content instanceof Request ? $content->input($key) : $content;

            $operations = $this->checkRules($checkContent, explode('|', $rule));

            $conditionals = array_flip($this->conditional);

            $normalRules = array_diff_key($operations, $conditionals);
            $foundConditionals = array_intersect_key($operations, $conditionals);

            // Override Rules
            if (in_array(true, $foundConditionals)) {
                foreach ($normalRules as $k => $v) {
                    $normalRules[$k] = true;
                }
            }

            // Set Error Messages
            foreach ($normalRules as $opKey => $opValue) {
                if ($opValue === false) {
                    $this->errors[$key] = $this->messages[$opKey] ?? $opValue;
                }
            }
        }
    }

    public function validationErrors(): array
    {
        return $this->errors;
    }

    public function validationFails(): bool
    {
        return empty($this->errors) !== true;
    }

    private function integer($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function string($value): bool
    {
        // Some level of comfort but not the final solution
        return filter_var($value, FILTER_SANITIZE_STRING) == $value;
    }

    private function nullable($value): bool
    {
        return $value === null;
    }

    private function min($value, $characters): bool
    {
        return strlen($value) >= (int)$characters;
    }

    private function max($value, $characters): bool
    {
        return strlen($value) <= (int)$characters;
    }

    public function timestamp($value): bool
    {
        $pattern = '/^[0-9]{4}-([0][1-9]|[1][0-2])-([0][1-9]|[1-2][0-9]|[3][01]) ([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';

        return preg_match($pattern, $value) === 1;
    }
}