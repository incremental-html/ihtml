<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;

class DisplayRule extends CcsRule
{
    public static function rule(): string
    {
        return 'display';
    }

    public static function method(): string
    {
        return 'display';
    }
}
