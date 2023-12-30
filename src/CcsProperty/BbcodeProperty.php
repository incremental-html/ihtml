<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */
class BbcodeProperty extends Property
{
    public static function apply(Crawler $list, array $params): void
    {
        $parser = new Parser();
        $parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
        foreach ($list as $element) {
            $content = static::solveParams($params, $element);
            $content = $parser->parse($content)->getAsHtml();
            while ($element->hasChildNodes()) {
                $element->removeChild($element->firstChild);
            }
            if (!$content) {
                break;
            }
            $html = Property::domFragment($content, $element->ownerDocument);
            $element->appendChild($html);
        }
    }
}
