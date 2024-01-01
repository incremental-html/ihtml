<?php
declare(strict_types=1);

namespace iHTML\CcsProperty\Traits;

use iHTML\CcsParser\CcsFunctions;
use iHTML\DOM\DOMElement;
use iHTML\iHTML\DocumentQuery;
use Sabberworm\CSS\Value\CSSFunction;

trait ContentTrait
{
    protected static function solveParams(array $params, DOMElement $entry, DocumentQuery $context): string
    {
        $content = [];
        foreach ($params as $param) {
            $content[] = match (true) {
                is_string($param) => $param,
                $param instanceof CSSFunction => CcsFunctions::{$param->getName()}(
                    ...$param->getArguments(),
                    context: [
                        'element' => $entry,
                        'root' => $context->getWorkingDir(),
                    ],
                ),
                is_callable($param) => $param($entry),
            };
        }
        return implode($content);
    }
}