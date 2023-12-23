<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;
use Exception;

class DocumentQueryAttribute
{
    private Crawler $nodelist;
    private DocumentQuery $query;
    private string $name;

    // TODO:
    const CONTENT = 2003;
    // const DISPLAY = 2004;
    const NONE    = 2005;
    // function display($value)
    // const VISIBLE = 2006;
    // const HIDDEN  = 2007;
    // function visibility($value)

    public function __construct(DocumentQuery $query, Crawler $nodelist, string $name)
    {
        $this->query    = $query;
        $this->nodelist = $nodelist;
        $this->name     = $name;
    }

    public function __invoke($value)
    {
        return $this->content($value);
    }

    public function content($value)
    {
        foreach ($this->nodelist as $entry) {
            $entry->setAttribute($this->name, $value);
        }
        return $this->query;
    }

    public function display($value)
    {
        foreach ($this->nodelist as $entry) {
            if ($value === self::NONE) {
                $entry->removeAttribute($this->name);
            } else {
                throw new Exception('attribute/not NONE Not yet implemented.');
            }
            $entry->setAttribute('style', $style);
        }
        return $this->query;
    }

    public function visibility($value)
    {
        throw new Exception('attribute/visibiliy Not yet implemented.');
    }
}
