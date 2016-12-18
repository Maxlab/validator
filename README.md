# RespectValidation for Simple validation of data (post,forms, etc.) ([ENG doc](README.md) | [RU doc](README_RU.md)) 

## What is it?
This is a small library based on [RespectValidation](https://github.com/Respect/Validation) to simplify the assignment of validation rules receipt and error. Therefore, if you are not familiar with RespectValidation library, for a greater understanding of the example below, it is recommended to familiarize yourself first with her.


## Exemple
We got the dataset from $_POST:
```php
    
    $data = array(
        'first_name' => 'Nikolay',
        'last_name' => 1,
        'addresses' => array(
            2 => array(
                'city' => 'Some City',
                'street' => 'Some Street 4',
                'home' => '4b',
                'apartment' => 2,
                'email' => 'notvalidemail',
            ),
            5 => array(
                'city' => 'Some City',
                'street' => 'Some Street 10',
                'home' => '1a',
                'apartment' => 10,
                'email' => 'valid@email.com',
                'siblings_emails' => array(
                    2 => array(
                        'email' => 'notvalidemail'
                    ),
                    8 => array(
                        'email' => 'valid@email.com'
                    ),
                )
            ),
            10 => array(
                'city' => 'Ясный - 1$$',
                'street' => 'Some Street 10',
                'home' => '1a',
                'apartment' => 10,
                'email' => 'valid@email.com',
                'siblings_emails' => array(
                    2 => array(
                        'email' => 'notvalidemail'
                    ),
                    8 => array(
                        'email' => 'valid@email.com'
                    ),
                )
            ),
        ),
    );
    
```

Often, in the course of work on the project there is a need to connect your own validator classes.
- In this example, the folder `\Maxlab\Validation\Examples\Rules` is the class Alcinum.php
- Below, we talk RespectValidation the location of the additional rules and exceptions.(see details in the documentation [RespectValidation](https://github.com/Respect/Validation))
```php
    
    \Respect\Validation\Validator::with('\\Maxlab\\Validation\\Examples\\Rules', true);
    
```

Set validation rules:
```php
    
    $rules = [
        'first_name' => \Respect\Validation\Validator::stringType()
            ->postalCode('RU'),
        'last_name' => \Respect\Validation\Validator::stringType()
            ->notEmpty(),
        '\..*.email' => \Respect\Validation\Validator::email(),
        'addresses.*.city' => \Respect\Validation\Validator::stringType()->length(1, 150)->alcinum('- '),
    ];
    
```

Define our messages for errors:
```php
    
    $locale = new \Maxlab\Validation\Examples\Lang\Ru();
    $validator = new \Maxlab\Validation\DataValidator($data, $rules, $locale);
    
    if($errors = $validator->getErrors()) {
        var_dump($errors);
    }
    
```

Get in $errors:
```no-highlight
array(3) {
  ["first_name"]=> string(46) "first_name must be a valid postal code on "RU""
  ["last_name"]=> string(51) "должно быть строковым типом"
  ["addresses"]=>
  array(3) {
    [2]=>
    array(1) {
      ["email"]=>
      string(59) "не является валидным email адресом"
    }
    [5]=>
    array(1) {
      ["siblings_emails"]=>
      array(2) {
        [2]=>
        array(1) {
          ["email"]=>
          string(59) "не является валидным email адресом"
        }
        [8]=>
        string(59) "не является валидным email адресом"
      }
    }
    [10]=>
    array(2) {
      ["city"]=>
      string(62) "только символы (А-я), (A-z), цифры и "- ""
      ["siblings_emails"]=>
      array(2) {
        [2]=>
        array(1) {
          ["email"]=>
          string(59) "не является валидным email адресом"
        }
        [8]=>
        string(59) "не является валидным email адресом"
      }
    }
  }
}

```

## В шаблонизаторе (twig)
```twig
    
    {% if formerrors['addresses'][k]['city'] is defined %}
        <div class="b_form_error">
            {{ formerrors['addresses'][k]['city'] }}
        </div>
    {% endif %}
    
    
```

## Обьяснение
As you can see, the simplicity and power of assignment rules make a regular expression. This feature eliminates the need to copy-paste rules and to describe each field separately. Also, this approach makes it more convenient to assign rules for nested data.
Using regexps, you can do crazy things:
- `\..*.email` - to providerwith all the data with a key `email` recursively
- to do so `addresses.*.id` or so `addresses.[\d].id` or even so `addresses.[0-9]{1,3}.id`
- using regular expressions opens wide horizons in terms of binding the validation rules to nested data

## Conclusion
This approach fits well for the validation of the nested data. It could be a $_POST array or the data from the Api, the data from the DB and so on.
If you have ideas or suggestions on how to improve or optimize the operation of the library, then you should offer them in the form of a pullrequest.

## More examples!

```php
    
    public function getFormRules($formdata)
    {
        $drule = [];
        $drule['address'] = $formdata['formstate']['new_addr'] == 'no_edit' ? '\.(?!new).*\.' : '\..*\.';
        
        $rules = [
            'user\..*' => v::stringType(),
            'user.first_name' => v::length(0, 150)->alcinum('\'" '),
            'user.last_name' => v::length(0, 150)->alcinum('\'" '),
            'user.phone' => v::optional(v::length(1, 150)->phone()),
            'user.email' => v::notEmpty()->length(1, 150)->email(),
            
            'user.b_day' => [
                'rule' => v::optional(v::length(1, 2)->between(1, 31)),
                'name' => 'user.birth_day',
            ],
            'user.b_month' => [
                'rule' => v::optional(v::length(1, 2)->between(1, 12)),
                'name' => 'user.birth_day',
            ],
            'user.b_year' => [
                'rule' => v::optional(v::length(1, 4)->between(1910, date('Y'))),
                'name' => 'user.birth_day',
            ],
            'user.birth_day' => v::optional(v::date()),
            
            'addresses'.$drule['address'].'(?!id|index|comment)' => v::stringType(),
            'addresses'.$drule['address'].'city' => v::notEmpty()->length(1, 255)->alcinum('\'"\\- '),
            'addresses'.$drule['address'].'street' => v::notEmpty()->length(1, 255)->alcinum('\'"\\- '),
            'addresses'.$drule['address'].'home' => v::notEmpty()->length(1, 20),
            'addresses'.$drule['address'].'build' => v::optional(v::length(1, 20)),
            'addresses'.$drule['address'].'room' => v::optional(v::length(1, 20)),
            'customer_details\..*' => v::stringType()->optional(v::length(1, 500)),
            
            'change_password.old_password' => v::optional(v::stringType()->length(1, 255)->userPasswordCheck()),
            'change_password.new_password' => v::optional(v::stringType()->length(1, 255)->alnum("#*&%$")->noWhitespace()),
        ];
        
        return $rules;
    }
    
```




