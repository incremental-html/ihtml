<?php

namespace iHTML\CcsProperty;

use DOMElement;
use Parsedown;

class MarkdownProperty extends Property
{
    public function apply(DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $content = $this->parsedown->text($content);

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }

    public $parsedown;

    public function __construct($domdocument)
    {
        parent::__construct($domdocument);

        $this->parsedown = new Parsedown();
    }
}
