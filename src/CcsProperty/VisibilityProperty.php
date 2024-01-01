<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use Exception;
use iHTML\CcsProperty\Traits\InheritanceTrait;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use iHTML\iHTML\DocumentQuery;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class VisibilityProperty extends Property
{
    use InheritanceTrait;

    public const VISIBLE = 1003;
    public const HIDDEN = 1004;
    public const CCS = [
        'visible' => self::VISIBLE,
        'hidden' => self::HIDDEN,
    ];

    /**
     * @param DocumentQuery $context
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params, DocumentQuery $context): void
    {
        if (count($params) > 1) {
            throw new Exception('Bad parameters count: ' . json_encode($params));
        }
        $valid = [
            VisibilityProperty::VISIBLE,
            VisibilityProperty::HIDDEN,
        ];
        if (!in_array($params[0], $valid)) {
            throw new Exception('Bad parameters: ' . json_encode($params));
        }
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $element->setAttribute('data-visibility', (string)$params[0]);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
        $attributeName = 'data-visibility';
        $list = (new Crawler($domDocument))
            ->filter("[$attributeName]")
        ;
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $attribute = $element->getAttribute($attributeName);
            $element->removeAttribute($attributeName);
            if ($attribute == self::HIDDEN) {
                $element->remove();
            }
        }
    }
}
