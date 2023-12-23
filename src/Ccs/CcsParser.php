<?php


namespace iHTML\Ccs;

use iHTML\Messages\File;
use Sabberworm\CSS;
use Exception;
use Closure;
use Webmozart\PathUtil\Path;

class CcsParser
{
    private Closure $onSelectorEvent;
    private Closure $onImportEvent;
    const   INHERITANCE_TREE = 3;
    const   INHERITANCE_LIST = 4;

    public function setOnImport(callable $onImport): self
    {
        $this->onImportEvent = Closure::fromCallable($onImport);
        return $this;
    }

    public function setOnSelector(callable $onSelector): self
    {
        $this->onSelectorEvent = Closure::fromCallable($onSelector);
        return $this;
    }

    public function parse(string $code, \Directory $root): self
    {
        $oCssParser = ( new CSS\Parser($code) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            if ($oContent instanceof CSS\Property\Import) {
                ($this->onImportEvent)($oContent->atRuleArgs()[0]->getUrl()->getString(), $root);
            } elseif ($oContent instanceof CSS\RuleSet\DeclarationBlock) {
                if (empty($oContent->getRules())) {
                    continue;
                }
                // selectors_weight(...$oContent->getSelectors()); // TODO

                $rules = array_map(fn ($oRule) => new class($oRule, $root) {
                    public function __construct($oRule, $root)
                    {
                        $this->name    = $oRule->getRule();
                        $this->content = (string)$oRule->getValue();
                        $this->values  = $oRule->getValue() instanceof CSS\Value\RuleValueList ? $oRule->getValue()->getListComponents() : [ $oRule->getValue() ];
                        $this->values = array_map(
                            fn ($v) =>
                                $v instanceof CSS\Value\URL ? new CSS\Value\CSSString(file_get_contents(Path::makeAbsolute($v->getURL()->getString(), $root->path))) :
                                // var(--something) ? ... :
                                $v,
                            $this->values
                        );
                    }
                    public string $name;
                    public string $content;
                    public array $values;
                }, $oContent->getRules());

                ($this->onSelectorEvent)(implode(',', $oContent->getSelectors()), $rules);
            } else {
                throw new Exception('Unexpected CSS element');
            }
        }
        return $this;
    }

    public function inheritanceFile(File $file): array
    {
        return $this->inheritanceCode($file->fread($file->getSize() + 1), dir($file->getPath()));
    }

    public function inheritanceCode(string $code, \Directory $root, int $style = self::INHERITANCE_LIST): array
    {
        $inheritance = [];
        $oCssParser = ( new CSS\Parser($code) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            switch (true) {
                case $oContent instanceof CSS\Property\Import:
                    $import = $oContent->atRuleArgs()[0]->getUrl()->getString();
                    $imports = (new CcsParser)
                        ->setFile($oContent->atRuleArgs()[0]->getUrl()->getString())
                        ->inheritance();
                    if ($style === self::INHERITANCE_LIST) {
                        $inheritance = array_merge($inheritance, $imports);
                        $inheritance[] = $import;
                    }
                    if ($style === self::INHERITANCE_TREE) {
                        $inheritance[$import] = array_merge($hierarchy[$import], $imports);
                    }
                break;
            }
        }
        return $inheritance;
    }
}
