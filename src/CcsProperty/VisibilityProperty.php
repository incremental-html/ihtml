<?php

namespace iHTML\CcsProperty;

use iHTML\Document\Modifiers\VisibilityModifier;

class VisibilityProperty extends Property
{
    public static function property(): string
    {
        return 'visibility';
    }

    public static function method(): string
    {
        return 'visibility';
    }

    public static function constants(): array
    {
        return [
            'visible' => VisibilityModifier::VISIBLE,
            'hidden' => VisibilityModifier::HIDDEN,
        ];
    }
}
