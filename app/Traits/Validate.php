<?php 
namespace App\Traits;

trait Validate
{
    protected function validateRequired($field)
    {
        if(empty($this->data[$field])){
            $message = $this->messages['required'] ?? ':attribute là trường bắt buộc!'; 
            $this->addError($field,$message);
        }
    }

    protected function validateEmail($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $message = $this->messages['email'] ?? ':attribute không hợp lệ!'; 
            $this->addError($field,$message);
        }
    }

    protected function validateMin($field, $parameters)
    {
        $min = (int)$parameters[0];
        if (isset($this->data[$field]) && (strlen($this->data[$field]) < $min)) {
            $message = $this->messages['min'] ?? ':attribute tối thiểu :min kí tự!'; 
            $this->addError($field,$message,['min' => $min]);
        }
    }
    protected function validateMax($field, $parameters)
    {
        $max = (int)$parameters[0];
        if (isset($this->data[$field]) && (strlen($this->data[$field]) > $max)) {
            $message = $this->messages['max'] ?? ':attribute tối thiểu :max kí tự!'; 
            $this->addError($field,$message,['max' => $max]);
        }
    }

    protected function validateInteger($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $message = $this->messages['email'] ?? ':attribute không phải kiểu integer!'; 
            $this->addError($field,$message);
        }
    }
}