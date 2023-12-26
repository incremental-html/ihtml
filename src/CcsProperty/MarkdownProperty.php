<?php

namespace iHTML\CcsProperty;

use DOMElement;
use Parsedown;

class MarkdownProperty extends Property
{
    public Parsedown $parser;

    public function __construct($domDocument)
    {
        parent::__construct($domDocument);

        $this->parser = new Parsedown();
    }

    public function apply(DOMElement $element): void
    {
        $content = static::solveParams($this->params, $element);

        $content = $this->parser->text($content);

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }
}
