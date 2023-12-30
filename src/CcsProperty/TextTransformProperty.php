<?php

namespace iHTML\CcsProperty;

use DOMText;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

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

    /**
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params): void
    {
        if (!self::isValid(...$params)) {
            throw new Exception("Bad parameters: " . json_encode($params));
        }
        $later = Property::applyLater($list, $params, self::INHERIT);
        $later = parent::laterExpandInherits($later);
        $transforms = [
            self::LOWERCASE => 'strtolower',
            self::UPPERCASE => 'strtoupper',
            self::CAPITALIZE => 'ucwords',
        ];
        foreach ($later as $late) {
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

    private static function isValid(...$params): bool
    {
        return in_array($params[0], [self::UPPERCASE, self::LOWERCASE, self::CAPITALIZE, self::INHERIT]);
    }
}
