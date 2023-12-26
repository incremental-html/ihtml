<?php

namespace iHTML\iHTML;

use DOMDocument;
use Exception;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use Masterminds\HTML5;
use Sabberworm\CSS\Parsing\SourceException;
use Symfony\Component\DomCrawler\Crawler;
use function Symfony\Component\String\u;

class Document
{
    private HTML5 $parser;
    private DOMDocument $domDocument;
    private array $modifiers = [];

    /**
     * @throws SourceException
     * @throws Exception
     */
    public function __construct(FileRegularExistent $htmlFile)
    {
        $this->parser = new HTML5;
        $this->domDocument = $this->parser->load($htmlFile, [HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true]);
        $this->ccsLinks($htmlFile);
        $this->ccsNodes($htmlFile);
        $this->ccsAttributes($htmlFile);
    }

    // implements $document('SELECTOR') ...
    public function __invoke($selector): DocumentQuery
    {
        $query = (new Crawler($this->domDocument))->filter($selector);
        return new DocumentQuery($this, $query);
    }

    // final rendering
    public function render(): Document
    {
        foreach ($this->modifiers as $modifier) {
            $modifier->render();
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function save(string $output, string $outputDir, string $index = "index.html"): Document
    {
        $file = self::fileFromResource($output, $index, $outputDir);
        $file->getPath()->create();
        $this->parser->save($this->domDocument, $file);
        return $this;
    }

    public function print(): Document
    {
        print $this->parser->saveHTML($this->domDocument);
        return $this;
    }

    public function get(): string
    {
        return $this->parser->saveHTML($this->domDocument);
    }

    /**
     * @throws Exception
     */
    public function getModifier(string $modifier)
    {
        // modifiersMap maps modifiers method with classes, in form of: [ method => class, ... ]
        $modifierClass = '\\iHTML\\CcsProperty\\' . u($modifier)->title() . 'Property';
        if (!class_exists($modifierClass)) {
            throw new Exception("Class `$modifierClass` not implemented for method `$modifier`.");
        }
        $this->modifiers[$modifier] = new $modifierClass($this->domDocument);
        return $this->modifiers[$modifier];
    }

    /**
     * @param string $output
     * @param string $index
     * @param string $outputDir
     * @return FileRegular
     * @throws Exception
     */
    private static function fileFromResource(string $output, string $index, string $outputDir): FileRegular
    {
        $file = $output ?: './';
        if (str_ends_with($file, '/')) {
            $file .= $index;
        }
        return new FileRegular($file, $outputDir);
    }

    /**
     * @param FileRegularExistent $htmlFile
     * @return void
     * @throws SourceException
     * @throws Exception
     */
    private function ccsLinks(FileRegularExistent $htmlFile): void
    {
        foreach ($this('link[rel="contentsheet"][href]') as $result) {
            $ccsFile = new FileRegularExistent($result->getAttribute('href'), $htmlFile->getPath());
            $ccs = Ccs::fromFile($ccsFile);
            $ccs->applyTo($this);
            $result->remove();
        }
    }

    /**
     * @param FileRegularExistent $htmlFile
     * @return void
     * @throws SourceException
     * @throws Exception
     */
    private function ccsNodes(FileRegularExistent $htmlFile): void
    {
        foreach ($this('content') as $result) {
            $ccs = Ccs::fromString($result->textContent, $htmlFile->getPath());
            $ccs->applyTo($this);
            $result->remove();
        }
    }

    /** @noinspection PhpUnusedParameterInspection */
    private function ccsAttributes(FileRegularExistent $htmlFile): void
    {
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        foreach ($this('[content]') as $result) {
        // TODO
        }
    }
}
