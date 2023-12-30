<?php

namespace iHTML\CcsProperty;

use Parsedown;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */
class MarkdownProperty extends Property
{
    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            $parser = new Parsedown();
            $content = static::solveParams($params, $element);
            $content = $parser->text($content);
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
