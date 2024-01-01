<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use iHTML\CcsProperty\Traits\ContentTrait;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class BorderProperty extends Property
{
    use ContentTrait;

    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $content = static::solveParams($params, $element);
            $fragment = $element->document()->fragmentFromString($content);
            $element->parentNode->replaceChild($fragment, $element);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}
