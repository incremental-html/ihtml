<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;
use iHTML\Document\Modifiers\VisibilityModifier;

class VisibilityRule extends CcsRule
{
    public static function rule(): string
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
