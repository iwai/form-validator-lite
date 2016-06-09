<?php

/**
 * Created by PhpStorm.
 * User: iwai
 * Date: 2016/06/09
 * Time: 17:22
 */

namespace Iwai;

class FormValidatorLite
{
    protected $rules = array();
    protected $messages = array();

    function __construct($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data)
    {
        foreach ($this->rules as $name => $rule) {
            foreach ($rule as $condition) {
                list($validator, $options) = each($condition);

                $validator_func = '\Iwai\FormValidatorLite\Validator::' . $validator;

                if (!function_exists($validator_func)) {
                    if (function_exists($validator)) {
                        $validator_func = $validator;
                    } else {
                        throw new \RuntimeException(sprintf('Not found function %s', $validator));
                    }
                }

                if ($options['require']) {
                    list($valid, $message) = \Iwai\FormValidatorLite\Validator::DataRequire($name, $data, $options);

                    if (!$valid) {
                        $this->messages[ $name ] = $message;
                        break;
                    }
                }

                list($valid, $message) = $validator_func($name, $data, $options);

                if (!$valid) {
                    $this->messages[ $name ] = $message;
                    break;
                }
            }
        }

        return count($this->messages) > 0;
    }

    /**
     * @param string|null $name
     * @return array
     */
    public function getMessages($name = null)
    {
        if ($name != null) {
            return $this->messages[$name];
        }

        return $this->messages;
    }
}