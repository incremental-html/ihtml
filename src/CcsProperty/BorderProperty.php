<?php

namespace iHTML\CcsProperty;

use DOMElement;

class BorderProperty extends Property
{
    public function apply(DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $element->parentNode->replaceChild($this->domFragment($content), $element);
    }
}
