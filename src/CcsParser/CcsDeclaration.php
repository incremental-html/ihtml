<?php
declare(strict_types=1);

namespace iHTML\CcsParser;

use Exception;
use iHTML\Filesystem\FileRegularExistent;
use Sabberworm\CSS;

class CcsDeclaration
{
    public string $property;
    public string $rawValue;
    public array $values;

    public function __construct($oRule, $root)
    {
        $this->property = $oRule->getRule();
        $this->rawValue = (string)$oRule->getValue();
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