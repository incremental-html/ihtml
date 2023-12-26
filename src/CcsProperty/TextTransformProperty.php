<?php

namespace iHTML\CcsProperty;

use DOMElement;
use DOMText;

class TextTransformProperty extends Property
{
    const LOWERCASE = 1013;
    const UPPERCASE = 1014;
    const CAPITALIZE = 1015;
    const NONE = 1016;

    public static function ccsConstants(): array
    {
        $ccsConstants = parent::ccsConstants();
        return $ccsConstants + [
                'uppercase' => TextTransformProperty::UPPERCASE,
                'lowercase' => TextTransformProperty::LOWERCASE,
                'capitalize' => TextTransformProperty::CAPITALIZE,
                'none' => TextTransformProperty::NONE,
            ];
    }

    public static function isValid(...$params): bool
    {
        return in_array($params[0], [self::UPPERCASE, self::LOWERCASE, self::CAPITALIZE, self::INHERIT]);
    }

    public function apply(DOMElement $element): void
    {
        $this->applyLater($element, self::INHERIT);
    }

    public function render(): void
    {
        parent::latesExpandInherits();

        $transforms = [
            self::LOWERCASE => 'strtolower',
            self::UPPERCASE => 'strtoupper',
            self::CAPITALIZE => 'ucwords',
        ];

        foreach ($this->lates as $late) {
            if ($late->attribute == self::NONE) {
                continue;
            }

            // replace in all text child nodes
            for ($i = 0; $i < $late->element->childNodes->length; $i++) {
                $childNode = $late->element->childNodes[$i];
                if ($childNode instanceof DOMText) {
                    $text = $transforms[$late->attribute]($childNode->wholeText);
                    $late->element->replaceChild($late->element->ownerDocument->createTextNode($text), $childNode);
                }
            }
        }
    }
}
