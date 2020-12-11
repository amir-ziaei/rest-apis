<?php

namespace App\Traits;

use Rakit\Validation\Validator;

trait Parsley {

    protected $validationParams;
    protected $rules;
    protected $validation;
    protected $errors = null;
    protected $firstOfAllError = null;

    public function ParsleyRun($rules)
    {
        if(! $this->setValidationParams()) {
            $this->firstOfAllError = ["Bad Parameters Format: Expected JSON"];
            return false;
        }

        $this->setRules($rules)
                 ->validate()
                 ->anyError()
                 ->getTheError();

        if($this->firstOfAllError) {
            return false;
        }

        return true;
    }

    protected function setValidationParams()
    {
        $json_params = HTTP_RAW_POST_DATA();

        if (strlen($json_params) > 0 && $this->isValidJSON($json_params))
        {
            $decoded_params = (array) json_decode($json_params);
            if( ! isset($decoded_params['data'])) {
                // TODO: Take care of the exception
                throw new \Exception("Data missing");
            }
            foreach ($decoded_params['data'] as $key => $value) {
                $decoded_params[$key] = safe($value);
            }
            $this->validationParams = $decoded_params;
            return $this;
        }

        return false;
    }

    protected function setRules($rules)
    {
        $this->rules = (array) $rules;
        return $this;
    }

    protected function validate()
    {
        $validator = new Validator;
        $this->validation = $validator->make($this->validationParams, $this->rules);
        $this->validation->validate();

        return $this;
    }

    protected function anyError()
    {
        if ($this->validation->fails()) {
            $this->errors = $this->validation->errors();
        }

        return $this;
    }

    protected function getTheError()
    {
        if(! is_null($this->errors))
            $this->firstOfAllError = $this->errors->firstOfAll();
    }

    protected function isValidJSON($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }
}