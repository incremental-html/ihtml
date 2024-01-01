<?php
declare(strict_types=1);

namespace iHTML\CcsProperty\Traits;

use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use Symfony\Component\DomCrawler\Crawler;

trait InheritanceTrait
{
    public static function expandAttribute(string $attributeName, DOMDocument $domDocument): void
    {
        $crawler = new Crawler($domDocument);
        $list = $crawler->filter("[$attributeName]");
        $list = iterator_to_array($list);
        usort(
            $list,
            fn($a, $b) => substr_count($a->getNodePath(), '/') -
                substr_count($b->getNodePath(), '/'),
        );
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $crawler = new Crawler($element);
            $childElements = $crawler->filter('*');
            foreach ($childElements as $childElement) {
                /** @var DOMElement $childElement */
                if ($childElement->hasAttribute($attributeName)) {
                    continue;
                }
                $childElement->setAttribute(
                    $attributeName,
                    $element->getAttribute($attributeName),
                );
            }
        }
    }
}