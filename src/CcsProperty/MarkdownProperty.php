<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use iHTML\CcsProperty\Traits\ContentTrait;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use iHTML\iHTML\DocumentQuery;
use Parsedown;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class MarkdownProperty extends Property
{
    use ContentTrait;

    public static function apply(Crawler $list, array $params, DocumentQuery $context): void
    {
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $parser = new Parsedown();
            $content = static::solveParams($params, $element, $context);
            $content = $parser->text($content);
            $element->empty();
            if (!$content) {
                continue;
            }
            $element->appendContent($content);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}
