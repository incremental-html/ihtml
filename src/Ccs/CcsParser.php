<?php


namespace iHTML\Ccs;

use Closure;
use Exception;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use Sabberworm\CSS;

class CcsParser
{
    private Closure $onSelectorEvent;
    private Closure $onImportEvent;
    const   INHERITANCE_TREE = 3;
    const   INHERITANCE_LIST = 4;

    public function onImport(callable $onImport): self
    {
        $this->onImportEvent = $onImport(...);
        return $this;
    }

    public function onSelector(callable $onSelector): self
    {
        $this->onSelectorEvent = $onSelector(...);
        return $this;
    }

    public function parse(string $code, FileDirectoryExistent $root): self
    {
        $cssParser = new CSS\Parser($code);
        $oCssParser = $cssParser->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            switch (true) {
                case $oContent instanceof CSS\Property\Import:
                    $url = $oContent->atRuleArgs()[0]->getUrl()->getString();
                    $this->onImportExecute($url, $root);
                    break;
                case $oContent instanceof CSS\RuleSet\DeclarationBlock:
                    $rules = $oContent->getRules();
                    if (empty($rules)) {
                        continue 2;
                    }
                    // selectors_weight(...$oContent->getSelectors()); // TODO
                    $map = fn($oRule) => new class($oRule, $root) {
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
                    };
                    $rules = array_map($map, $rules);
                    $selectors = $oContent->getSelectors();
                    $this->onSelectorExecute($selectors, $rules);
                    break;
                default:
                    throw new Exception('Unexpected CSS element');
            }
        }
        return $this;
    }

    public function inheritanceFile(FileRegular $file): array
    {
        return $this->inheritanceCode($file->contents($file->getSize() + 1), dir($file->getPath()));
    }

    public function inheritanceCode(string $code, \Directory $root, int $style = self::INHERITANCE_LIST): array
    {
        $inheritance = [];
        $oCssParser = (new CSS\Parser($code))->parse();
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

    public function onImportExecute(string $url, FileDirectoryExistent $root): void
    {
        ($this->onImportEvent)($url, $root);
    }

    private function onSelectorExecute(array $selectors, array $rules)
    {
        ($this->onSelectorEvent)($selectors, $rules);
    }
}
