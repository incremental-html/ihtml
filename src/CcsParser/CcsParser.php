<?php


namespace iHTML\CcsParser;

use Closure;
use Exception;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegular;
use Sabberworm\CSS;
use Sabberworm\CSS\Parsing\SourceException;

class CcsParser
{
    private Closure $onRuleEvent;
    private Closure $onImportEvent;
    const   INHERITANCE_TREE = 3;
    const   INHERITANCE_LIST = 4;

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
     * @throws SourceException
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
                        $inheritance[$import] = array_merge($inheritance[$import], $imports);
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

    private function onRuleExecute(array $selectors, array $declarations)
    {
        ($this->onRuleEvent)($selectors, $declarations);
    }
}
