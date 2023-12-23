<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;

class MarkdownRule extends CcsRule
{
    public static function rule(): string
    {
        return 'markdown';
    }

    public static function method(): string
    {
        return 'markdown';
    }
}
