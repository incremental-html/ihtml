<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMText;
use Exception;
use iHTML\CcsProperty\Traits\InheritanceTrait;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use iHTML\iHTML\DocumentQuery;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class WhiteSpaceProperty extends Property
{
    use InheritanceTrait;

    public const INHERIT = 1005;  // collapse: W+N      Text wrap: when necessary
    public const NORMAL = 1006;   // collapse: W+N      Text wrap: when necessary
    public const NOWRAP = 1007;   // collapse: W+N      Text wrap: preserve
    public const PRE = 1008;      // collapse: -        Text wrap: preserve
    public const PRELINE = 1009;  // collapse: W        Text wrap: when necessary
    public const PREWRAP = 1010;  // collapse: -        Text wrap: when necessary
    // public const INITIAL = 1011; // Sets this property to its default value. Read about initial
    public const CCS = parent::CCS + [
        'normal' => self::NORMAL,
        'nowrap' => self::NOWRAP,
        'pre' => self::PRE,
        'pre-line' => self::PRELINE,
        'pre-wrap' => self::PREWRAP,
    ];
    private const REGEX = [
        self::NORMAL => ['/[ \t\r\n]+/' => ' '],
        self::NOWRAP => ['/[ \t\r\n]+/' => ' '], // in future not wraps
        self::PRELINE => ['/[ \t]*[\r\n][ \t]*/' => "\n", '/[ \t]+/' => ' '],
        // PREWRAP like PRE and in future waps
    ];

    /**
     * @param DocumentQuery $context
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params, DocumentQuery $context): void
    {
        /** @var DOMElement[] $list */
        if (count($params) > 1) {
            throw new Exception('Bad parameters count: ' . json_encode($params));
        }

        $valid = [
            WhiteSpaceProperty::NORMAL,
            WhiteSpaceProperty::NOWRAP,
            WhiteSpaceProperty::PRE,
            WhiteSpaceProperty::PRELINE,
            WhiteSpaceProperty::PREWRAP,
            WhiteSpaceProperty::INHERIT,
        ];
        if (!in_array($params[0], $valid)) {
            throw new Exception('Bad parameters: ' . json_encode($params));
        }
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $element->setAttribute('data-whitespace', (string)$params[0]);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
        $attributeName = 'data-whitespace';
        // $default = self::INHERIT;
        self::expandAttribute($attributeName, $domDocument);
        $list = (new Crawler($domDocument))
            ->filter("[$attributeName]")
        ;
        foreach ($list as $element) {
            /** @var DOMElement $element */
            self::applyToElement($element, $attributeName);
        }
    }

    private static function applyToElement(DOMElement $element, string $attributeName): void
    {
        $attribute = $element->getAttribute($attributeName);
        $element->removeAttribute($attributeName);
        if ($attribute == self::PRE) {
            return;
        }
        $regex = array_keys(self::REGEX[$attribute]);
        $replace = array_values(self::REGEX[$attribute]);
        for ($counter = 0; $counter < $element->childNodes->length; $counter++) {
            $childNode = $element->childNodes[$counter];
            if (!$childNode instanceof DOMText) {
                continue;
            }
            $text = preg_replace($regex, $replace, $childNode->wholeText);
            $element->replaceChild($element->ownerDocument->createTextNode($text), $childNode);
        }
    }
}
