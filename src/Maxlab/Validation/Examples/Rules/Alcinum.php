<?php
namespace Maxlab\Validation\Examples\Rules;
/**
 * Created by PhpStorm.
 * Author: Stepanov N.
 *
 * Только цифры, символы рус и анг алфавитов, а также дополнительные символы
 */

use Respect\Validation\Rules\AbstractRule;

class Alcinum extends AbstractRule
{
    public $additionalChars = '';

    public function __construct($additionalChars = '')
    {
        if (!is_string($additionalChars)) {
            throw new \Exception('Invalid list of additional characters to be loaded');
        }

        $this->additionalChars .= $additionalChars;
    }

    public function validate($input)
    {
        $match = '/[^А-Яа-яA-Za-z\x{0410}-\x{042F}\d';
        if($this->additionalChars) $match .= $this->additionalChars;

        if(preg_match($match.'^]/u', $input)){
            return false;
        } else {
            return true;
        }
    }
}

