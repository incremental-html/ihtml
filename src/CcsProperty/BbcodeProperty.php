<?php

namespace iHTML\CcsProperty;

use DOMElement;
use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;

class BbcodeProperty extends Property
{
    public Parser $parser;

    public function __construct($domDocument)
    {
        parent::__construct($domDocument);

        $this->parser = new Parser();
        $this->parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
    }

    public function apply(DOMElement $element): void
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
}
