<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;

class BorderRule extends CcsRule
{
    public static function rule(): string
    {
        return 'border';
    }

    public static function method(): string
    {
        return 'border';
    }
}
