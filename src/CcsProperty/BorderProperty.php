<?php

namespace iHTML\CcsProperty;

use DOMElement;

class BorderProperty extends Property
{
    public static function queryMethod(): string
    {
        return 'border';
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
