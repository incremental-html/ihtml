<?php

namespace iHTML\CcsProperty;

use DOMElement;
use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;

class BbcodeProperty extends Property
{
    public function apply(DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $content = $this->parser->parse($content)->getAsHtml();

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }

    public $parser;

    public function __construct($domdocument)
    {
        parent::__construct($domdocument);

        $this->parser = new Parser();
        $this->parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
    }
}
