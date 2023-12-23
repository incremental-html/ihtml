<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;
use iHTML\Document\Modifiers\TextTransformModifier;

class TexttransformRule extends CcsRule
{
    public static function rule(): string
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
