<?php
declare(strict_types=1);

namespace iHTML\CcsParser;

use Exception;
use iHTML\Filesystem\FileRegularExistent;

class CcsFunctions
{
    public static function url(string $urlPath, array $context): string
    {
        $file = new FileRegularExistent($urlPath, $context['root']);
        return $file->contents();
    }

    public static function var(string $variableName, array $context): string
    {
        return match ($variableName) {
            '--content' => $context['element']->content(),
            '--display' => $context['element']->display(),
            default => throw new Exception("Variable $variableName not supported."),
        };
    }

    public static function attr(string $attributeName, array $context): string
    {
        if (!$context['element']->hasAttribute($attributeName)) {
            throw new Exception("Attribute $attributeName not found.");
        }
        return $context['element']->getAttribute($attributeName);
    }
}