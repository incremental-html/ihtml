<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class DocumentQueryClass
{
    private Crawler $nodelist;
    private DocumentQuery $query;
    private string $name;

    const VISIBLE = 3001;
    const HIDDEN  = 3002;

    public function __construct(DocumentQuery $query, Crawler $nodelist, string $name)
    {
        $this->query    = $query;
        $this->nodelist = $nodelist;
        $this->name     = $name;
    }

    public function __invoke($value)
    {
        return $this->visibility($value);
    }

    public function visibility($value)
    {
        foreach ($this->nodelist as $entry) {
            $classes = preg_split('/\s+/', $entry->getAttribute('class'));
            if ($value === self::HIDDEN  && in_array($this->name, $classes)) {
                $classes = array_diff($classes, [$this->name]);
            }
            if ($value === self::VISIBLE && !in_array($this->name, $classes)) {
                $classes[] = $this->name;
            }
            $entry->setAttribute('class', implode(' ', $classes));
        }
        return $this->query;
    }
}
