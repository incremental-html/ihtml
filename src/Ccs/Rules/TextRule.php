<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;

class TextRule extends CcsRule
{
    public static function rule(): string
    {
        return 'text';
    }

    public static function method(): string
    {
        return 'text';
    }
}
