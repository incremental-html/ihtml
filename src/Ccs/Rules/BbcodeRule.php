<?php

namespace iHTML\Ccs\Rules;

class BbcodeRule extends \iHTML\Ccs\CcsRule
{
    public static function rule():   string
    {
        return 'bbcode';
    }
    public static function method(): string
    {
        return 'bbcode';
    }
}
