<?php


namespace iHTML\CcsParser;

use Closure;
use Exception;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use iHTML\iHTML\Ccs;
use Sabberworm\CSS;

class CcsParser
{
    private Closure $onRuleEvent;
    private Closure $onImportEvent;

    public function onImport(callable $onImport): self
    {
        $this->onImportEvent = $onImport(...);
        return $this;
    }

    public function onRule(callable $onRule): self
    {
        $this->onRuleEvent = $onRule(...);
        return $this;
    }

    /**
     * @throws CSS\Parsing\SourceException
     */
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
                    /**
                     * @see https://web.dev/learn/css/selectors#the_parts_of_a_css_rule
                     */
                    $declarations = collect($oContent->getRules());
                    if ($declarations->isEmpty()) {
                        continue 2;
                    }
                    $declarations = $declarations
                        ->map(fn($oRule) => new CcsDeclaration($oRule, $root))
                        ->toArray()
                    ;
                    // selectors_weight(...$oContent->getSelectors()); // TODO
                    $selectors = $oContent->getSelectors();
                    $this->onRuleExecute($selectors, $declarations);
                    break;
                default:
                    throw new Exception('Unexpected CSS element');
            }
        }
        return $this;
    }

    private function onImportExecute(string $url, FileDirectoryExistent $root): void
    {
        ($this->onImportEvent)($url, $root);
    }

    private function onRuleExecute(array $selectors, array $declarations): void
    {
        ($this->onRuleEvent)($selectors, $declarations);
    }
}
