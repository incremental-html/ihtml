<?php

namespace iHTML\Document\Modifiers;

class BbcodeModifier extends \iHTML\Document\DocumentModifier
{
    public static function queryMethod(): string
    {
        return 'bbcode';
    }
    
    public static function isValid(...$params): bool
    {
        return true;
    }

    public function apply(\DOMElement $element)
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
    
        $this->parser = new \JBBCode\Parser();
        $this->parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
    }
}
