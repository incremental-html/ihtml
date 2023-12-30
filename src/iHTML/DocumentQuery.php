<?php

namespace iHTML\iHTML;

use Exception;
use iHTML\CcsProperty\Property;
use IteratorAggregate;
use Symfony\Component\DomCrawler\Crawler;

class DocumentQuery implements IteratorAggregate
{
    private Document $document;
    private Crawler $query;

    public function __construct(Document $document, Crawler $query)
    {
        $this->document = $document;
        $this->query = $query;
    }

    /**
     * @throws Exception
     */
    public function __call(string $modifierMethod, $arguments)
    {
        $modifierClass = $this->document->getModifier($modifierMethod);
        /** @var Property $modifierClass */
        $modifierClass::apply($this->query, $arguments);
    }

    public function getIterator(): Crawler
    {
        return $this->query;
    }
}
