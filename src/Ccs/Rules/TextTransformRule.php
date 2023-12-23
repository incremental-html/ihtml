<?php

namespace iHTML\Ccs\Rules;

class TexttransformRule extends \iHTML\Ccs\CcsRule
{
    public static function rule():      string
    {
        return 'text-transform';
    }
    public static function method():    string
    {
        return 'textTransform';
    }
    public static function constants(): array
    {
        return parent::constants() + [
        'uppercase'  => \iHTML\Document\Modifiers\TextTransformModifier::UPPERCASE,
        'lowercase'  => \iHTML\Document\Modifiers\TextTransformModifier::LOWERCASE,
        'capitalize' => \iHTML\Document\Modifiers\TextTransformModifier::CAPITALIZE,
        'none'       => \iHTML\Document\Modifiers\TextTransformModifier::NONE,
    ];
    }
}
