<?php

namespace iHTML\Document\Modifiers;

class TextModifier extends \iHTML\Document\DocumentModifier
{
    public static function queryMethod(): string
    {
        return 'text';
    }
    
    public static function isValid(...$params): bool
    {
        return true;
    }

    public function apply(\DOMElement $element)
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
