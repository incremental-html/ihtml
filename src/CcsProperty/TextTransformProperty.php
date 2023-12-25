<?php

namespace iHTML\CcsProperty;

use iHTML\Document\Modifiers\TextTransformModifier;

class TexttransformProperty extends Property
{
    public static function property(): string
    {
        return 'text-transform';
    }

    public static function method(): string
    {
        return 'textTransform';
    }

    public static function constants(): array
    {
        return parent::constants() + [
                'uppercase' => TextTransformModifier::UPPERCASE,
                'lowercase' => TextTransformModifier::LOWERCASE,
                'capitalize' => TextTransformModifier::CAPITALIZE,
                'none' => TextTransformModifier::NONE,
            ];
    }
}
