<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use Symfony\Component\DomCrawler\Crawler;

abstract class Property
{
    public const CCS = [];

    public function __construct(
        protected DOMDocument $domDocument,
    )
    {
    }

    abstract public static function apply(Crawler $list, array $params): void;

    abstract public static function render(DOMDocument $domDocument): void;
}
