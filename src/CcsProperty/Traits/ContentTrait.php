<?php
declare(strict_types=1);

namespace iHTML\CcsProperty\Traits;

use iHTML\CcsParser\CcsFunctions;
use iHTML\DOM\DOMElement;
use Sabberworm\CSS\Value\CSSFunction;

trait ContentTrait
{
    protected static function solveParams(array $params, DOMElement $entry): string
    {
        $content = [];
        foreach ($params as $param) {
            $content[] = match (true) {
                is_string($param) => $param,
                $param instanceof CSSFunction => CcsFunctions::{$param->getName()}(
                    ...$param->getArguments(),
                    context: ['element' => $entry],
                ),
                is_callable($param) => $param($entry),
            };
        }
        return implode($content);
    }
}