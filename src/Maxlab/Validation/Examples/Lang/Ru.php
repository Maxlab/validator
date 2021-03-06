<?php

namespace Maxlab\Validation\Examples\Lang;

use Maxlab\Validation\Lang\ILang;

/**
 * Класс локализации, и перевода сообщений об ошибках на русский язык.
 *
 * Created by PhpStorm.
 * Author: Stepanov N.
 */
class Ru implements ILang
{
    /**
     * Ключи - имена правил ( примеры /vendor/respect/validation/library/Exceptions/ )
     * Просто посмотрите классы исключений и вам станет понятно, почему структура массива именно такая.
     *
     * @var array
     */
    protected $messages = [
        'email' => [
            1 => [
                'не является валидным email адресом',
            ],
            2 => [
                'является валидным email адресом',
            ],
        ],
        'notEmpty' => [
            1 => [
                'не должно быть пустым',
                'не должно быть пустым',
            ],
            2 => [
                'должно быть пустым',
                'должно быть пустым',
            ],
        ],
        'stringType' => [
            1 => [
                'должно быть строковым типом',
            ],
            2 => [
                'не должно быть строковоым типом',
            ],
        ],
        'phone' => [
            1 => [
                'не является валидным номером телефона',
            ],
            2 => [
                'является не номером телефона',
            ],
        ],
        'length' => [
            1 => [
                'длина должна быть между {{minValue}} и {{maxValue}}',
                'длина должна быть больше {{minValue}}',
                'длина должна быть меньше {{maxValue}}',
            ],
            2 => [
                'длина не должна быть между {{minValue}} и {{maxValue}}',
                'длина не должна быть больше {{minValue}}',
                'длина не должна быть меньше {{maxValue}}',
            ],
        ],
        'alnum' => [
            1 => [
                'только символы (a-z) и цифры (0-9)',
                'только символы (a-z), цифры (0-9) и {{additionalChars}}',
            ],
            2 => [
                'не должно быть символов (a-z) и цифр (0-9)',
                'не должно быть символов (a-z), цифр (0-9) и {{additionalChars}}',
            ],
        ],
        'noWhitespace' => [
            1 => [
                'не должно быть пробелов',
            ],
            2 => [
                'должны быть пробелы',
            ],
        ],
        'date' => [
            1 => [
                'должна быть валидная дата',
                'должна быть валидная дата, формата: {{format}}',
            ],
            2 => [
                'не должна быть валидная дата',
                'не должна быть валидная дата, формата: {{format}}',
            ],
        ],

    ];

    public function getMessages()
    {
        return $this->messages;
    }
}
