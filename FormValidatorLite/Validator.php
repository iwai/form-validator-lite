<?php
/**
 * Created by PhpStorm.
 * User: iwai
 * Date: 2016/06/09
 * Time: 17:17
 */

namespace Iwai\FormValidatorLite;


class Validator
{
    /**
     * @param $name
     * @param $params
     * @param array $options
     * @return array
     */
    static public function DataRequire($name, $params, $options)
    {
        $data = $params[$name];

        $data = trim($data);
        if ($data === null || $data == '') {
            $message = sprintf('Require %s', $name);
            if (isset($options['messageRequire'])) {
                $message = $options['messageRequire'];
            } elseif (isset($options['message'])) {
                $message = $options['message'];
            }

            return array(false, $message);
        }
        return array(true, null);
    }

    /**
     *
     * min: 3
     * max: 100
     * messageMinimum: "メールアドレスは3文字以上で入力してください"
     * messageMaximum: "メールアドレスは100文字以内で入力してください"
     *
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function StringLength($name, $params, $options)
    {
        $data = $params[$name];

        if (isset($options['min'])) {
            if (function_exists('mb_strlen')) {
                if (mb_strlen($data) < $options['min']) {
                    return array(false, $options['messageMinimum']);
                }
            } else {
                if (strlen($data) < $options['min']) {
                    return array(false, $options['messageMinimum']);
                }
            }
        }
        if (isset($options['max'])) {
            if (function_exists('mb_strlen')) {
                if (mb_strlen($data) > $options['max']) {
                    return array(false, $options['messageMaximum']);
                }
            } else {
                if (strlen($data) > $options['max']) {
                    return array(false, $options['messageMaximum']);
                }
            }
        }
        return array(true, null);
    }

    /**
     *
     * message: "数字のみで入力してください"
     *
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function Numericality($name, $params, $options)
    {
        $data = $params[$name];

        if (!is_numeric($data)) {
            return array(false, $options['message']);
        }

        return array(true, null);
    }

    /**
     *
     * pattern: '/^[0-9]+$/'
     * message: "数字のみで入力してください"
     *
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function Regex($name, $params, $options)
    {
        $data = $params[$name];

        if (preg_match($options['pattern'], $data) !== 1) {
            return array(false, $options['message']);
        }

        return array(true, null);
    }

    /**
     *
     * values: [ 1,2,3,4,5 ]
     * multiple: false
     * message: "お問い合わせ種別を選択してください"
     *
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function InclusionIn($name, $params, $options)
    {
        $data = $params[$name];

        if (isset($options['require']) && !$options['require']) {
            if ($data == null || $data == '' || (is_array($data) && count($data) == 0)) {
                return array(true, null);
            }
        }

        if (is_array($data) && count($data) > 1) {
            if (!isset($options['multiple']) || !$options['multiple']) {
                return array(false, $options['message']);
            }
        } else {
            $data = is_array($data) ? $data : array($data);
        }

        foreach ($data as $value) {
            if (!in_array($value, $options['values'])) {
                return array(false, $options['message']);
            }
        }

        return array(true, null);
    }

    /**
     *
     * min: 2
     * max: 3
     * messageMinimum: "お問い合わせ種別は２つ以上３つ以下で選択してください"
     * messageMaximum: "お問い合わせ種別は２つ以上３つ以下で選択してください"
     *
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function MultipleLength($name, $params, $options)
    {
        $data = $params[$name];

        if (isset($options['require']) && !$options['require']) {
            if ($data == null || $data == '' || (is_array($data) && count($data) == 0)) {
                return array(true, null);
            }
        }

        if (!is_array($data)) {
            return array(false, $options['messageMinimum']);
        }
        if (isset($options['min']) && count($data) < $options['min']) {
            return array(false, $options['messageMinimum']);
        }
        if (isset($options['max']) && count($data) > $options['max']) {
            return array(false, $options['messageMaximum']);
        }
        return array(true, null);
    }

    /**
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function Email($name, $params, $options)
    {
        $data = $params[$name];

        if (isset($options['require']) && !$options['require']) {
            if ($data == null || $data == '') {
                return array(true, null);
            }
        }
        $pattern =
            '/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!'.
            '#\$\%&\'*+\\/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:'.
            '[a-zA-Z0-9_!#\$\%&\'*+\\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\\/=?\^`'.
            "{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/"
        ;
        // @see http://blog.livedoor.jp/dankogai/archives/51189905.html
        if (1 !== preg_match($pattern, $data)) {
            return array(false, $options['message']);
        }
        return array(true, null);
    }

    /**
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function URL($name, $params, $options)
    {
        $data = $params[$name];

        if (isset($options['require']) && !$options['require']) {
            if ($data == null || $data == '') {
                return array(true, null);
            }
        }

        if (!filter_var($data, FILTER_VALIDATE_URL)) {
            return array(false, $options['message']);
        }
        return array(true, null);
    }

    /**
     * @param $name
     * @param $params
     * @param $options
     * @return array
     */
    static public function PhoneNumber($name, $params, $options)
    {
        $data = $params[$name];

        if (isset($options['require']) && !$options['require']) {
            if ($data == null || $data == '') {
                return array(true, null);
            }
        }

        if (preg_match('/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{2,4}$/', $data) !== 1) {
            return array(false, $options['message']);
        }

        return array(true, null);
    }
}