<?php

namespace iHTML\CcsProperty;

use DOMElement;

class DisplayProperty extends Property
{
    public static function queryMethod(): string
    {
        return 'display';
    }

    public static function isValid(...$params): bool
    {
        return true;
    }

    public function apply(DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $element->parentNode->replaceChild($this->domFragment($content), $element);
    }
}
