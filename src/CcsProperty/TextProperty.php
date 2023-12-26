<?php

namespace iHTML\CcsProperty;

use DOMElement;

class TextProperty extends Property
{
    public function apply(DOMElement $element): void
    {
        $content = static::solveParams($this->params, $element);

        $content = nl2br(htmlentities($content));

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }
}
