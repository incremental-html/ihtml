<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use Parsedown;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class MarkdownProperty extends Property
{
    use ContentTrait;

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
            $element->appendChild(self::domFragment($content, $element->ownerDocument));
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}
