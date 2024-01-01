<?php
declare(strict_types=1);

namespace iHTML\CcsParser;

use Exception;
use Sabberworm\CSS\Value\CSSString;

class CcsFunctions
{
    public static function var(string $variableName, array $context): CSSString
    {
        $variableValue = match ($variableName) {
            '--content' =>
            new CSSString(collect($context['element']->childNodes)
                ->map(fn($n) => $context['element']->ownerDocument->saveHTML($n))
                ->join('')),
            '--display' =>
            new CSSString($context['element']->ownerDocument->saveHTML($context['element'])),
            default => throw new Exception("Variable $variableName not supported."),
        };
        return $variableValue;
    }
}