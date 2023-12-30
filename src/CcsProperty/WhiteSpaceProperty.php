<?php

namespace iHTML\CcsProperty;

use DOMText;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use function in_array;

/** @noinspection PhpUnused */

class WhiteSpaceProperty extends Property
{
    const NORMAL = 1006; // collapse: W+N      Text wrap: when necessary
    const NOWRAP = 1007; // collapse: W+N      Text wrap: preserve
    const PRE = 1008; // collapse: -        Text wrap: preserve
    const PRELINE = 1009; // collapse: W        Text wrap: when necessary
    const PREWRAP = 1010; // collapse: -        Text wrap: when necessary

    //const INITIAL = 1011; // Sets this property to its default value. Read about initial

    public static function ccsConstants(): array
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

    /**
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params): void
    {
        if (count($params) > 1) {
            throw new Exception("Bad parameters count: " . json_encode($params));
        }
        $valid = [
            WhiteSpaceProperty::NORMAL,
            WhiteSpaceProperty::NOWRAP,
            WhiteSpaceProperty::PRE,
            WhiteSpaceProperty::PRELINE,
            WhiteSpaceProperty::PREWRAP,
            Property::INHERIT,
        ];
        if (!in_array($params[0], $valid)) {
            throw new Exception("Bad parameters: " . json_encode($params));
        }
        $later = Property::applyLater($list, $params, self::INHERIT);
        $later = parent::laterExpandInherits($later);
        $regexes = [
            self::NORMAL => ['/[ \t\r\n]+/' => ' '],
            self::NOWRAP => ['/[ \t\r\n]+/' => ' '], // in future not wraps
            self::PRELINE => ['/[ \t]*[\r\n][ \t]*/' => "\n", '/[ \t]+/' => ' '],
            // PREWRAP like PRE and in future waps
        ];
        foreach ($later as $late) {
            if ($late->attribute == self::PRE) {
                continue;
            }
            $regex = array_keys($regexes[$late->attribute]);
            $replace = array_values($regexes[$late->attribute]);
            // replace in all text child nodes
            for ($i = 0; $i < $late->element->childNodes->length; $i++) {
                $childNode = $late->element->childNodes[$i];
                if ($childNode instanceof DOMText) {
                    $text = preg_replace($regex, $replace, $childNode->wholeText);
                    $late->element->replaceChild($late->element->ownerDocument->createTextNode($text), $childNode);
                }
            }
        }
    }
}
