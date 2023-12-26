<?php

namespace iHTML\iHTML;

use Exception;
use IteratorAggregate;
use Symfony\Component\DomCrawler\Crawler;

class DocumentQuery implements IteratorAggregate
{
    private Document $document;
    private Crawler $query;

    public function getIterator(): Crawler
    {
        return $this->query;
    }

    public function __construct(Document $document, Crawler $query)
    {
        $this->document = $document;
        $this->query = $query;
    }

    /**
     * @throws Exception
     */
    public function __call(string $modifierMethod, array $arguments)
    {
        $modifier = $this->document->getModifier($modifierMethod);
        $modifier->setList($this->query);
        return $modifier(...$arguments);
    }
//
//    public function attr($name, $value = null)
//    {
//        if (func_num_args() == 1) {
//            return new DocumentQueryAttribute($this, $this->query, $name);
//        }
//        (new DocumentQueryAttribute($this, $this->query, $name))($value);
//        return $this;
//    }
//
//    public function style($name, $value = null)
//    {
//        if (func_num_args() == 1) {
//            return new DocumentQueryStyle($this, $this->query, $name);
//        }
//        (new DocumentQueryStyle($this, $this->query, $name))($value);
//        return $this;
//    }
//
//    public function className($name, $value = null)
//    {
//        if (func_num_args() == 1) {
//            return new DocumentQueryClass($this, $this->query, $name);
//        }
//        (new DocumentQueryClass($this, $this->query, $name))($value);
//        return $this;
//    }
//
//    public function jsonLD($name, $value = null)
//    {
//        if (func_num_args() == 1) {
//            return new DocumentQueryJson($this, $this->query, $name);
//        }
//        (new DocumentQueryJson($this, $this->query, $name))($value);
//        return $this;
//    }
}
