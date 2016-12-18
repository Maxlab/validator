# RespectValidation for Simple validation of data (post,forms, etc.) ([ENG doc](README.md) | [RU doc](README_RU.md)) 

## Что это?
Это небольшая библиотека на основе [RespectValidation](https://github.com/Respect/Validation) для более удобного назначения правил валидации и получения ошибок. Поэтому, если вы еще не знакомы с RespectValidation библиотекой, то для большего понимания нижеидущего примера рекомендуем ознакомиться сначала с ней.


## Exemple
Мы получили массив данных из $_POST:
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

Часто в ходе работы над проектом возникает потребность в подключении своих собственные классов валидации.
- В этом примере в папке `\Maxlab\Validation\Examples\Rules` находится класс Alcinum.php
- Ниже мы сообщаем RespectValidation местоположение дополнительных правил и исключений.(подробности см. в документации [RespectValidation](https://github.com/Respect/Validation))
```php
    
    \Respect\Validation\Validator::with('\\Maxlab\\Validation\\Examples\\Rules', true);
    
```

Задаем правила валидации:
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

Определяем свои сообщения для ошибок:
```php
    
    $locale = new \Maxlab\Validation\Examples\Lang\Ru();
    $validator = new \Maxlab\Validation\DataValidator($data, $rules, $locale);
    
    if($errors = $validator->getErrors()) {
        var_dump($errors);
    }
    
```

Получаем в $errors:
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
Как вы могли заметить, удобство и мощь в назначении правил вносят регулярные выражения. Эта особенность снимает необходимость копипастить правила и описывать каждое поле в отдельности. Также, такой подход, делает более удобным назначение правил для вложенных данных. 
С помощью регулярок можно делать безумные вещи:
- `\..*.email` - провалидировать все данные с ключем `email` рекурсивно
- сделать так `addresses.*.id` или так `addresses.[\d].id` или даже так `addresses.[0-9]{1,3}.id`
- использование регулярных выражений открывает широкие горизонты в плане привязки правил валидации к вложенным данным

## Заключение
Такой подход, хорошо ложится на валидацию вложенных данных. Это может быть массив $_POST или данные из Api, данные из БД и тд.
Если у вас есть идеи или предложения, как можно улучшить или оптимизировать работу библиотеки, то вам стоит предлагать их в виде pullrequest.


## Больше примеров!

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




