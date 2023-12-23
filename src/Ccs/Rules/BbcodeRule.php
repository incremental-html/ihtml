<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;

class BbcodeRule extends CcsRule
{
    public static function rule(): string
    {
        return 'bbcode';
    }

    public static function method(): string
    {
        return 'bbcode';
    }
}
