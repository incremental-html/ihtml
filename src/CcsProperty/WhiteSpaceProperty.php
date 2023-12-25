<?php

namespace iHTML\CcsProperty;

class WhiteSpaceProperty extends Property
{
    public static function queryMethod(): string
    {
        return 'whiteSpace';
    }

    public static function isValid(...$params): bool
    {
        return in_array($params[0], [self::NORMAL, self::NOWRAP, self::PRE, self::PRELINE, self::PREWRAP, self::INHERIT]);
    }

    const NORMAL = 1006; // collapse: W+N      Text wrap: when necessary
    const NOWRAP = 1007; // collapse: W+N      Text wrap: preserve
    const PRE = 1008; // collapse: -        Text wrap: preserve
    const PRELINE = 1009; // collapse: W        Text wrap: when necessary
    const PREWRAP = 1010; // collapse: -        Text wrap: when necessary

    //const INITIAL = 1011; // Sets this property to its default value. Read about initial

    public static function constants(): array
    {
        $ccsConstants = parent::ccsConstants();
        return $ccsConstants + [
                'normal' => WhiteSpaceProperty::NORMAL,
                'nowrap' => WhiteSpaceProperty::NOWRAP,
                'pre' => WhiteSpaceProperty::PRE,
                'pre-line' => WhiteSpaceProperty::PRELINE,
                'pre-wrap' => WhiteSpaceProperty::PREWRAP,
            ];
    }

    public function apply(\DOMElement $element)
    {
        $this->applyLater($element, self::INHERIT);
    }

    public function render()
    {
        parent::latesExpandInherits();

        $regexes = [
            self::NORMAL => ['/[ \t\r\n]+/' => ' '],
            self::NOWRAP => ['/[ \t\r\n]+/' => ' '], // in future not wraps
            self::PRELINE => ['/[ \t]*[\r\n][ \t]*/' => "\n", '/[ \t]+/' => ' '],
            // PREWRAP like PRE and in future waps
        ];

        foreach ($this->lates as $late) {
            if ($late->attribute == self::PRE) {
                continue;
            }

            $regex = array_keys($regexes[$late->attribute]);
            $replace = array_values($regexes[$late->attribute]);
            // replace in all text child nodes
            for ($i = 0; $i < $late->element->childNodes->length; $i++) {
                $childNode = $late->element->childNodes[$i];
                if ($childNode instanceof \DOMText) {
                    $text = preg_replace($regex, $replace, $childNode->wholeText);
                    $late->element->replaceChild($late->element->ownerDocument->createTextNode($text), $childNode);
                }
            }
        }
    }
}
