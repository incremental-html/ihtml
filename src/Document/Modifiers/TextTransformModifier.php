<?php

namespace iHTML\Document\Modifiers;

class TextTransformModifier extends \iHTML\Document\DocumentModifier
{
    const LOWERCASE  = 1013;
    const UPPERCASE  = 1014;
    const CAPITALIZE = 1015;
    const NONE       = 1016;

    public static function queryMethod(): string
    {
        return 'textTransform';
    }
    
    public static function isValid(...$params): bool
    {
        return in_array($params[0], [self::UPPERCASE, self::LOWERCASE, self::CAPITALIZE, self::INHERIT]);
    }

    public function apply(\DOMElement $element)
    {
        $this->applyLater($element, self::INHERIT);
    }

    public function render()
    {
        parent::latesExpandInherits();

        $transforms = [
            self::LOWERCASE  => 'strtolower',
            self::UPPERCASE  => 'strtoupper',
            self::CAPITALIZE => 'ucwords',
        ];
        
        foreach ($this->lates as $late) {
            if ($late->attribute == self::NONE) {
                continue;
            }

            // replace in all text child nodes
            for ($i = 0; $i < $late->element->childNodes->length; $i++) {
                $childNode = $late->element->childNodes[ $i ];
                if ($childNode instanceof \DOMText) {
                    $text = $transforms[$late->attribute]($childNode->wholeText);
                    $late->element->replaceChild($late->element->ownerDocument->createTextNode($text), $childNode);
                }
            }
        }
    }
}
