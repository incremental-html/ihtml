<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class DisplayProperty extends Property
{
    use ContentTrait;

    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            $element->parentNode->replaceChild(self::domFragment($content, $element->ownerDocument), $element);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}
