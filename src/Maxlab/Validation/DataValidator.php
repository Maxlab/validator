<?php

namespace Maxlab\Validation;

use Maxlab\Validation\Lang\ILang;
use Maxlab\Validation\Lang\Ru;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class DataValidator
{
    const ERR_MODE_LAST = 1;
    const ERR_MODE_ALL = 2;

    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var array
     */
    protected $rules = [];
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * FormValidator constructor.
     *
     * @param array $data
     * @param array $rules
     * @param ILang $lang
     */
    public function __construct(array $data, array $rules, ILang $lang = null)
    {
        $this->data = json_decode(json_encode($data), true);
        $this->rules = $rules;

        (!$lang) ? $this->setMessages(new Ru()) : $this->setMessages($lang);
    }

    /**
     * @param int $mode
     *
     * @return array
     */
    public function getErrors($mode = self::ERR_MODE_LAST)
    {
        $errors = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->data),
            RecursiveIteratorIterator::SELF_FIRST);
        $messages = $this->getMessages();

        foreach ($iterator as $k => $v) {
            foreach ($this->rules as $key_rule => $rule) {
                try {
                    for ($p = array(), $i = 0, $z = $iterator->getDepth(); $i <= $z; ++$i) {
                        $p[] = $iterator->getSubIterator($i)->key();
                    }
                    $path = implode('.', $p) ?: $k;
                    if (preg_match('#'.$key_rule.'#', $path)) {
                        if (is_array($rule)) {
                            $rule['rule']->setName(!empty($rule['name']) ? $rule['name'] : $path);
                            $rule['rule']->check($v);
                        } else {
                            $rule->setName($path)
                                ->check($v);
                        }
                    }
                } catch (\Respect\Validation\Exceptions\ValidationException $e) {
                    if (!empty($messages[$e->getId()])) {
                        $e::$defaultTemplates = $messages[$e->getId()];
                        $e->setTemplate(null);
                        $e->getTemplate();
                    }

                    switch ($mode) {
                        case self::ERR_MODE_ALL:
                            Arr::set($errors, $e->getName().'.'.$e->getId(), $e->getMessage());
                            break;
                        case self::ERR_MODE_LAST:
                            Arr::set($errors, $e->getName(), $e->getMessage());
                            break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @param ILang $lang
     */
    public function setMessages(ILang $lang)
    {
        $this->messages = $lang->getMessages();
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $custom_messages
     */
    public function applyMessages(array $custom_messages)
    {
        $this->messages = array_merge($this->messages, $custom_messages);
    }
}
