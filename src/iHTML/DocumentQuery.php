<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use Exception;
use iHTML\CcsProperty\Property;
use IteratorAggregate;
use Symfony\Component\DomCrawler\Crawler;
use function Symfony\Component\String\u;

readonly class DocumentQuery implements IteratorAggregate
{
    public function __construct(
        private Document $document,
        private Crawler $query,
    )
    {
    }

    public function __call(string $method, array $arguments)
    {
        $modifierClass = $this->getPropertyClass($method);
        /** @var Property $modifierClass */
        $modifierClass::apply($this->query, $arguments);
        $this->document->appendRender($modifierClass);
    }

    private function getPropertyClass(string $method): string
    {
        // modifiersMap maps modifiers method with classes, in form of: [ method => class, ... ]
        $modifierClass = '\\iHTML\\CcsProperty\\' . u($method)->title() . 'Property';
        if (!class_exists($modifierClass)) {
            throw new Exception("Class `$modifierClass` not implemented for method `$method`.");
        }
        return $modifierClass;
    }

    public function getIterator(): Crawler
    {
        return $this->query;
    }
}
