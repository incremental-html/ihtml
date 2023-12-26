<?php

namespace iHTML\iHTML;

use DOMDocument;
use Exception;
use iHTML\CcsProperty\CcsChunk;
use iHTML\CcsProperty\CcsFile;
use iHTML\Filesystem\FileRegular;
use Masterminds\HTML5;
use Symfony\Component\DomCrawler\Crawler;
use function Symfony\Component\String\u;

class Document
{
    private HTML5 $parser;
    private DOMDocument $domDocument;
    private array $modifiers = [];

    public function __construct(FileRegular $html)
    {
        $this->parser = new HTML5;
        $this->domDocument = $this->parser->load($html, [HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true]);
        // LOAD INTERNAL CCS
        // <link rel="contentsheet" href="..."> ...
        foreach ($this('link[rel="contentsheet"][href]') as $result) {
            $ccs = new CcsFile(new FileRegular($result->getAttribute('href'), $html->getPath()));
            $ccs->applyTo($this);
            $result->parentNode->removeChild($result);
        }
        // <content> ... </content> ...
        foreach ($this('content') as $result) {
            $ccs = new CcsChunk($result->textContent, dir($html->getPath()));
            $ccs->applyTo($this);
            $result->parentNode->removeChild($result);
        }
        // <ELEM content="..."> ...
        // foreach ($this('[content]') as $result) {
        // TODO
        // }
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
}
