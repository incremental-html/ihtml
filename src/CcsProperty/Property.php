<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use Exception;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\CSSString;
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

    public static function convertPropertyValues(array $values): array
    {
        return collect($values)
            ->map(fn($value) => match (true) {
                $value instanceof CSSString => $value->getString(),
                is_string($value) => static::CCS[$value] ?? throw new Exception("Constant `$value` not defined."),
                $value instanceof CSSFunction && $value->getName() === 'var' =>
                match ($value->getArguments()[0]) {
                    '--content' => fn($element) => collect($element->childNodes)
                        ->map(fn($n) => $element->ownerDocument->saveHTML($n))
                        ->join(''),
                    '--display' => fn($element) => $element->ownerDocument->saveHTML($element),
                    default => throw new Exception("Variable {$value->getArguments()[0]} not supported."),
                },
                default => throw new Exception("Value $value not recognized."),
            })
            ->toArray()
        ;
    }
}
