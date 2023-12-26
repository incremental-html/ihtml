<?php

namespace iHTML\CcsProperty;

use DOMElement;
use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;

class BbcodeProperty extends Property
{
    public function apply(DOMElement $element): void
    {
        $parser = new Parser();
        $parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
        $content = static::solveParams($this->params, $element);
        $content = $parser->parse($content)->getAsHtml();
        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if (!$content) {
            return;
        }
        $element->appendChild($this->domFragment($content));
    }
}
