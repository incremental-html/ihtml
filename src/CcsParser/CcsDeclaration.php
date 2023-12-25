<?php

namespace iHTML\CcsParser;

use Exception;
use iHTML\Filesystem\FileRegularExistent;
use Sabberworm\CSS;

class CcsDeclaration
{
    public string $name;
    public string $content;
    public array $values;

    public function __construct($oRule, $root)
    {
        $this->name = $oRule->getRule();
        $this->content = (string)$oRule->getValue();
        $this->values = $oRule->getValue() instanceof CSS\Value\RuleValueList ?
            $oRule->getValue()->getListComponents() :
            [$oRule->getValue()];
        $this->values = array_map(
        /**
         * @throws Exception
         */
            fn($v) => $v instanceof CSS\Value\URL ?
                new CSS\Value\CSSString(
                    (
                    new FileRegularExistent($v->getURL()->getString(), $root)
                    )->contents()
                ) :
                // var(--something) ? ... :
                $v,
            $this->values
        );
    }
}