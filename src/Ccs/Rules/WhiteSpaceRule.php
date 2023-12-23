<?php

namespace iHTML\Ccs\Rules;

class WhitespaceRule extends \iHTML\Ccs\CcsRule
{
    public static function rule():      string
    {
        return 'white-space';
    }
    public static function method():    string
    {
        return 'whiteSpace';
    }
    public static function constants(): array
    {
        return parent::constants() + [
        'normal'   => \iHTML\Document\Modifiers\WhiteSpaceModifier::NORMAL,
        'nowrap'   => \iHTML\Document\Modifiers\WhiteSpaceModifier::NOWRAP,
        'pre'      => \iHTML\Document\Modifiers\WhiteSpaceModifier::PRE,
        'pre-line' => \iHTML\Document\Modifiers\WhiteSpaceModifier::PRELINE,
        'pre-wrap' => \iHTML\Document\Modifiers\WhiteSpaceModifier::PREWRAP,
    ];
    }
    //function isValid(...$params): bool { return in_array($params[0], [WhiteSpaceRule::NORMAL, WhiteSpaceRule::NOWRAP, WhiteSpaceRule::PRE, WhiteSpaceRule::PRELINE, WhiteSpaceRule::PREWRAP, WhiteSpaceRule::INHERIT]); }
}
