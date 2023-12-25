<?php

namespace iHTML\CcsProperty;

use DOMElement;

class DisplayProperty extends Property
{
    public function apply(DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $element->parentNode->replaceChild($this->domFragment($content), $element);
    }
}
