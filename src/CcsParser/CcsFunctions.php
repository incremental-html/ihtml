<?php
declare(strict_types=1);

namespace iHTML\CcsParser;

use Exception;

class CcsFunctions
{
    public static function var(string $variableName, array $context): string
    {
        return match ($variableName) {
            '--content' => $context['element']->content(),
            '--display' => $context['element']->display(),
            default => throw new Exception("Variable $variableName not supported."),
        };
    }
}