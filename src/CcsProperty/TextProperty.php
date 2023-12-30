<?php

namespace iHTML\CcsProperty;

use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */
class TextProperty extends Property
{
    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            $content = nl2br(htmlentities($content));
            while ($element->hasChildNodes()) {
                $element->removeChild($element->firstChild);
            }
            if (!$content) {
                return;
            }
            $element->appendChild(Property::domFragment($content, $element->ownerDocument));
        }
    }
}
