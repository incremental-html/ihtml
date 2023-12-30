<?php

namespace iHTML\CcsProperty;

use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */
class DisplayProperty extends Property
{
    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            $element->parentNode->replaceChild(Property::domFragment($content, $element->ownerDocument), $element);
        }
    }
}
