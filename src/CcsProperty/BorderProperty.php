<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */
class BorderProperty extends Property
{
    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            $html = Property::domFragment($content, $element->ownerDocument);
            $element->parentNode->replaceChild($html, $element);
        }
    }
}
