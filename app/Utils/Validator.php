<?php

namespace App\Utils;

use App\Traits\Validate;
use Exception;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Validator
{
    use Validate;
    private $data = [];
    private $rules = [];
    private $messages = [];
    private $attributes = [];

    private $errors = [];

    public function __construct($data, $rules, $messages = [], $attributes = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->attributes = $attributes;
        $this->handleValidate();
    }

    public function fails(){
        return !empty($this->errors);
    }

    public function flush(){
        $data = [
            'value' => $this->data,
            'errors' => $this->errors(), 
        ];
        session(true)->set('form',$data);
    }

    private function handleValidate(){
        foreach ($this->rules as $field => $ruleSet) {
            $rules = explode('|', $ruleSet);
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
    }

    protected function applyRule($field, $rule)
    {
        if (strpos($rule, ':') !== false) {
            list($ruleName, $parameters) = explode(':', $rule, 2);
            $parameters = explode(',', $parameters);
        } else {
            $ruleName = $rule;
            $parameters = [];
        }

        $method = 'validate' . ucfirst($ruleName);
        if (method_exists($this, $method)) {
            if(empty($parameters)){
                $this->$method($field);
            }else{
                $this->$method($field, $parameters);
            }
        } else {
            throw new Exception("Validate kiểu '{$ruleName}' không tồn tại.");
        }
    }

    protected function addError($field, $message, $replacements = []){

        $attribute = $this->attributes[$field] ?? $field;
        $message = str_replace(':attribute', $attribute, $message);

        foreach ($replacements as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }

        $this->errors[$field][] = $message;

    }
    public function errors(){
        return $this->errors;
    }
}
