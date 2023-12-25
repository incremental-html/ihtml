<?php

namespace iHTML\CcsProperty;

class MarkdownProperty extends Property
{
    public static function queryMethod(): string
    {
        return 'markdown';
    }
    
    public static function isValid(...$params): bool
    {
        return true;
    }

    public function apply(\DOMElement $element)
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
    
        $this->parsedown = new \Parsedown();
    }
}
