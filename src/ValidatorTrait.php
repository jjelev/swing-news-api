<?php

namespace Swing;

use UnexpectedValueException;

trait ValidatorTrait
{
    protected $messages = [
        'integer' => 'Value must be integer number',
        'string' => 'Text contains special characters',
        'min' => 'Minimum length of the text is not met',
        'max' => 'Text exceeds maximum length',
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

    /**
     * @param $content
     * @param string $rules
     */
    public function validate($content, string $rules): void
    {
        $operations = $this->checkRules($content, explode('|', $rules));

        $conditionals = array_flip($this->conditional);

        $normalRules = array_diff_key($operations, $conditionals);
        $foundSpecials = array_intersect_key($operations, $conditionals);

        // Override Rules
        if (in_array(true, $foundSpecials)) {
            foreach ($normalRules as $k => $v) {
                $normalRules[$k] = true;
            }
        }

        // Set Error Messages
        foreach ($normalRules as $opKey => $opValue) {
            if ($opValue === false) {
                $this->errors[$opKey] = $this->messages[$opKey];
            }
        }
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

    private function nullable($value)
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
}