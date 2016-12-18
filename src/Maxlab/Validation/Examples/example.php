<?php
/**
 * Author: Nikolai Stepanov
 * Date: 11.12.16
 */
require_once getcwd().'/../../../../vendor/autoload.php';

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

// set my own rules by namespace
\Respect\Validation\Validator::with('\\Maxlab\\Validation\\Examples\\Rules', true);

$rules = [
    'first_name' => \Respect\Validation\Validator::stringType()
        ->postalCode('RU'),
    'last_name' => \Respect\Validation\Validator::stringType()
        ->notEmpty(),
    '\..*.email' => \Respect\Validation\Validator::email(),
    'addresses.*.city' => \Respect\Validation\Validator::stringType()->length(1, 150)->alcinum('- '),
];

$locale = new \Maxlab\Validation\Examples\Lang\Ru();
$validator = new \Maxlab\Validation\DataValidator($data, $rules, $locale);

if($errors = $validator->getErrors()) {
    var_dump($errors);
    exit();
}

echo 'Formdata is Fine!';