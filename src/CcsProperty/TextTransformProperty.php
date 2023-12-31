<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use DOMElement;
use DOMText;
use Exception;
use iHTML\CcsProperty\Traits\InheritanceTrait;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class TextTransformProperty extends Property
{
    public const LOWERCASE = 1013;
    public const UPPERCASE = 1014;
    public const CAPITALIZE = 1015;
    public const NONE = 1016;
    public const INHERIT = 1017;
    public const CCS = parent::CCS + [
        'uppercase' => TextTransformProperty::UPPERCASE,
        'lowercase' => TextTransformProperty::LOWERCASE,
        'capitalize' => TextTransformProperty::CAPITALIZE,
        'none' => TextTransformProperty::NONE,
        'inherit' => TextTransformProperty::INHERIT,
    ];
    private const TRANSFORMATIONS = [
        self::LOWERCASE => 'strtolower',
        self::UPPERCASE => 'strtoupper',
        self::CAPITALIZE => 'ucwords',
    ];

    use InheritanceTrait;

    public static function apply(Crawler $list, array $params): void
    {
        if (count($params) > 1) {
            throw new Exception('Bad parameters count: ' . json_encode($params));
        }
        $valid = [
            TextTransformProperty::UPPERCASE,
            TextTransformProperty::LOWERCASE,
            TextTransformProperty::CAPITALIZE,
            TextTransformProperty::NONE,
            TextTransformProperty::INHERIT,
        ];
        if (!in_array($params[0], $valid)) {
            throw new Exception('Bad parameters: ' . json_encode($params));
        }
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $element->setAttribute('data-text-transform', (string)$params[0]);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
        $attributeName = 'data-text-transform';
        self::expandAttribute($attributeName, $domDocument);
        $list = (new Crawler($domDocument))
            ->filter("[$attributeName]")
        ;
        foreach ($list as $element) {
            self::applyToElement($element, $attributeName);
        }
    }

    private static function applyToElement(DOMElement $element, string $attributeName): void
    {
        $attribute = $element->getAttribute($attributeName);
        $element->removeAttribute($attributeName);
        if ($attribute == self::NONE) {
            return;
        }
        for ($counter = 0; $counter < $element->childNodes->length; $counter++) {
            $childNode = $element->childNodes[$counter];
            if ($childNode instanceof DOMText) {
                $text = self::TRANSFORMATIONS[$attribute]($childNode->wholeText);
                $element->replaceChild($element->ownerDocument->createTextNode($text), $childNode);
            }
        }
    }
}
