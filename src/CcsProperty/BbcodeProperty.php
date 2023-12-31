<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use iHTML\CcsProperty\Traits\ContentTrait;
use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class BbcodeProperty extends Property
{
    use ContentTrait;

    public static function apply(Crawler $list, array $params): void
    {
        $parser = new Parser();
        $parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            $content = $parser->parse($content)->getAsHTML();
            while ($element->hasChildNodes()) {
                $element->removeChild($element->firstChild);
            }
            if (!$content) {
                break;
            }
            $html = self::domFragment($content, $element->ownerDocument);
            $element->appendChild($html);
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}