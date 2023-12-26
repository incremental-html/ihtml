<?php

namespace iHTML\CcsProperty;

use DOMElement;
use Parsedown;

class MarkdownProperty extends Property
{
    public function apply(DOMElement $element): void
    {
        $parser = new Parsedown();
        $content = static::solveParams($this->params, $element);
        $content = $parser->text($content);
        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if (!$content) {
            return;
        }
        $element->appendChild($this->domFragment($content));
    }
}
