<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use iHTML\CcsProperty\Traits\ContentTrait;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use iHTML\iHTML\DocumentQuery;
use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class BbcodeProperty extends Property
{
    use ContentTrait;

    public static function apply(Crawler $list, array $params, DocumentQuery $context): void
    {
        $parser = new Parser();
        $parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $content = static::solveParams($params, $element, $context);
            $content = $parser->parse($content)->getAsHTML();
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