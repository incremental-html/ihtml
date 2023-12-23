<?php

namespace iHTML\Ccs\Rules;

use iHTML\Ccs\CcsRule;
use iHTML\Document\Modifiers\WhiteSpaceModifier;

class WhitespaceRule extends CcsRule
{
    public static function rule(): string
    {
        return 'white-space';
    }

    public static function method(): string
    {
        return 'whiteSpace';
    }

    public static function constants(): array
    {
        return parent::constants() + [
                'normal' => WhiteSpaceModifier::NORMAL,
                'nowrap' => WhiteSpaceModifier::NOWRAP,
                'pre' => WhiteSpaceModifier::PRE,
                'pre-line' => WhiteSpaceModifier::PRELINE,
                'pre-wrap' => WhiteSpaceModifier::PREWRAP,
            ];
    }
    //function isValid(...$params): bool { return in_array($params[0], [WhiteSpaceRule::NORMAL, WhiteSpaceRule::NOWRAP, WhiteSpaceRule::PRE, WhiteSpaceRule::PRELINE, WhiteSpaceRule::PREWRAP, WhiteSpaceRule::INHERIT]); }
}
