<?php

namespace Swing;

trait ValidatorTrait
{
    protected $errors;

    /**
     * @param $content
     * @param array $rules
     */
    public function validate($content, array $rules): void
    {

    }

    public function validationErrors()
    {
        return $this->errors ?? [];
    }

    public function validationFails()
    {
        return empty($this->errors) !== true;
    }
}