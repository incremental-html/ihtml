<?php

namespace iHTML\CcsProperty;

use DOMElement;

class ContentProperty extends Property
{
    public function apply(DOMElement $element): void
    {
        $content = static::solveParams($this->params, $element);
        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if (!$content) {
            return;
        }
        $element->appendChild($this->domFragment($content));
    }
}
