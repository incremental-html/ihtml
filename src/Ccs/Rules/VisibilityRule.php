<?php

namespace iHTML\Ccs\Rules;

class VisibilityRule extends \iHTML\Ccs\CcsRule
{
    public static function rule():      string
    {
        return 'visibility';
    }
    public static function method():    string
    {
        return 'visibility';
    }
    public static function constants(): array
    {
        return [
                'visible' => \iHTML\Document\Modifiers\VisibilityModifier::VISIBLE,
                'hidden'  => \iHTML\Document\Modifiers\VisibilityModifier::HIDDEN,
        ];
    }
}
