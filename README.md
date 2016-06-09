# Iwai\FormValidatorLite

## Install

```json
{
    "require": {
        "iwai/form-validator-lite": "*"
    }
}
```

## Usage

```php
use Iwai\FormValidatorLite;

$validator = new Iwai\FormValidatorLite(array(
    'user_name' => array(
      'StringLength' => array(
        'require' => true,
        'max' => 50,
        'messageRequire' => "お名前を入力してください",
        'messageMaximum' => "お名前は50文字以内で入力してください"
      )
    ),

    'user_email' => array(
      'StringLength' => array(
        'require' => true,
        'min' => 3,
        'max' => 100,
        'messageRequire' => "メールアドレスを入力してください",
        'messageMinimum' => "メールアドレスは3文字以上で入力してください",
        'messageMaximum' => "メールアドレスは100文字以内で入力してください"
      ),
      'Email' => array(
        'require' => true,
        'message' => "正しいメールアドレスを入力してください"
      ),
    )
));

if ($validator->validate($_POST)) {
  # Invalid process
  $error_messages = $validator->getMessage();
}

```
