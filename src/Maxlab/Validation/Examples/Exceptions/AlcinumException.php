<?php

namespace Maxlab\Validation\Examples\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class AlcinumException extends ValidationException
{
    const EXTRA = 1;

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'только символы (А-я), (A-z) и цифры',
            self::EXTRA => 'только символы (А-я), (A-z), цифры и {{additionalChars}}',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'не должно быть символов (А-я), (A-z) и цифр',
            self::EXTRA => 'не должно быть символов (А-я), (A-z), цифр, {{additionalChars}}',
        ],
    ];

    public function chooseTemplate()
    {
        return $this->getParam('additionalChars') ? static::EXTRA : static::STANDARD;
    }
}
