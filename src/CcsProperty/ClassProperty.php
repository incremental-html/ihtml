<?php

namespace iHTML\CcsProperty;

use DOMElement;
use Exception;

class ClassProperty extends Property
{
    const VISIBLE = 1003;
    const HIDDEN = 1004;

    public static function ccsConstants(): array
    {
        parent::ccsConstants();
        return [
            'visible' => VisibilityProperty::VISIBLE,
            'hidden' => VisibilityProperty::HIDDEN,
        ];
    }

    /**
     * @throws Exception
     */
    public function apply(DOMElement $element): void
    {
        if (count($this->params) % 2 > 0) {
            throw new Exception('Wrong `class` property values count');
        }
        $params = array_chunk($this->params, 2);
        foreach ($params as [$class, $visibility]) {
            if (!is_string($class)) {
                throw new Exception("Wrong `class` name ($class)");
            }
            if (!in_array($visibility, [self::VISIBLE, self::HIDDEN])) {
                throw new Exception("Wrong `class` visibility ($class)");
            }
            $classList = $element->getAttribute('class');
            $classList = (array)preg_split('/\s+/', $classList);
            $classList = array_filter($classList);
            $classList = array_flip($classList);
            switch ($visibility) {
                case self::VISIBLE:
                    $classList[$class] = true;
                    break;
                case self::HIDDEN:
                    unset($classList[$class]);
                    break;
                default:
            }
            $classList = array_keys($classList);
            $classList = implode(' ', $classList);
            $element->setAttribute('class', $classList);
        }
    }
}