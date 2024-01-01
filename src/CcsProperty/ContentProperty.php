<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use iHTML\CcsProperty\Traits\ContentTrait;
use iHTML\DOM\DOMDocument;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class ContentProperty extends Property
{
    use ContentTrait;

    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            while ($element->hasChildNodes()) {
                $element->removeChild($element->firstChild);
            }
            if (!$content) {
                continue;
            }
            $element->appendChild(self::domFragment($content, $element->ownerDocument));
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}
