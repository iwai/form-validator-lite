<?php

/**
 * Created by PhpStorm.
 * User: iwai
 * Date: 2016/06/09
 * Time: 17:22
 */

namespace Iwai;

use Iwai\FormValidatorLite\Validator;

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

                $namespace = '\Iwai\FormValidatorLite\Validator';

                if (!method_exists($namespace, $validator)) {
                    list ($namespace, $validator) = explode('::', $validator);

                    if (!method_exists($namespace, $validator)) {
                        throw new \RuntimeException(sprintf('Not found function %s', $validator));
                    }
                }

                if (!isset($options['require']) || $options['require'] == true) {
                    list($valid, $message) = \Iwai\FormValidatorLite\Validator::DataRequire($name, $data, $options);

                    if (!$valid) {
                        $this->messages[ $name ] = $message;
                        break;
                    }
                }

                list($valid, $message) = call_user_func_array(
                    array($namespace, $validator),
                    array($name, $data, $options)
                );

                if (!$valid) {
                    $this->messages[ $name ] = $message;
                    break;
                }
            }
        }

        return count($this->messages) === 0;
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