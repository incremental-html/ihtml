<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;

class ContentRule extends CcsRule
{
    public static function rule(): string
    {
        return 'content';
    }

    public static function method(): string
    {
        return 'content';
    }
}
