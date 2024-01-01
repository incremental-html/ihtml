<?php
declare(strict_types=1);

namespace iHTML\CcsProperty\Traits;

use iHTML\DOM\DOMElement;

trait ContentTrait
{
    protected static function solveParams(array $params, DOMElement $entry): string
    {
        $content = [];
        foreach ($params as $param) {
            $content[] = match (true) {
                is_string($param) => $param,
                is_callable($param) => $param($entry),
            };
        }
        return implode($content);
    }
}