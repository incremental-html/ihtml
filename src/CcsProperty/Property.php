<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

abstract class Property
{
    public const DISPLAY = 1001;
    public const CONTENT = 1002;
    public const NONE = 1005;
    public const INHERIT = 1012;
    public const CCS = [
        'display' => Property::DISPLAY,
        'content' => Property::CONTENT,
        'none' => Property::NONE,
        'inherit' => Property::INHERIT,
    ];

    public function __construct(
        protected DOMDocument $domDocument,
    )
    {
    }

    abstract public static function apply(Crawler $list, array $params): void;

    abstract public static function render(DOMDocument $domDocument): void;

    protected static function solveParams(array $params, DOMNode $entry): string
    {
        $content = [];
        foreach ($params as $param) {
            switch (true) {
                case is_string($param):
                    $content[] = $param;
                    break;
                case $param === self::NONE:
                    // none
                    break;
                case $param === self::DISPLAY:
                    $content[] = $entry->ownerDocument->saveHTML($entry);
                    break;
                case $param === self::CONTENT:
                    foreach ($entry->childNodes as $childNode) {
                        $content[] = $entry->ownerDocument->saveHTML($childNode);
                    }
                    break;
            }
        }
        return implode($content);
    }
}
