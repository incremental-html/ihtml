<?php

namespace iHTML\Document;

use DOMDocument;
use Exception;
use iHTML\Ccs\CcsChunk;
use iHTML\Ccs\CcsFile;
use iHTML\Filesystem\FileRegular;
use Masterminds\HTML5;
use SplFileInfo;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Path;

class Document
{
    private DOMDocument $domDocument;
    private array $modifiers = [];

    public function __construct(FileRegular $html)
    {
        $this->domDocument = (new HTML5)->load($html, [HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true]);
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

    public function save(string $output, string $outputDir, string $index = "index.html"): Document
    {
        $output = $output ?: './';
        if (str_ends_with($output, '/')) {
            $output = $output . $index;
        }
        $output = new FileRegular($output, $outputDir);
        if (!empty($output->getPath()) && !file_exists($output->getPath())) {
            mkdir($output->getPath(), 0777, true);
        }
        (new HTML5)->save($this->domDocument, $output);
        return $this;
    }

    public function print(): Document
    {
        print (new HTML5)->saveHTML($this->domDocument);
        return $this;
    }

    public function get(): string
    {
        return (new HTML5)->saveHTML($this->domDocument);
    }

    /**
     * @throws Exception
     */
    public function getModifier(string $modifier)
    {
        // modifiersMap maps modifiers method with classes, in form of: [ method => class, ... ]
        $modifiersMap =
            collect(scandir(__DIR__ . '/Modifiers'))
                ->diff(['.', '..'])
                ->map(fn($file) => '\\iHTML\\Document\\Modifiers\\' . Path::getFilenameWithoutExtension($file))
                ->mapWithKeys(fn($modifierClass) => [$modifierClass::queryMethod() => $modifierClass]);
        if (!$modifiersMap->has($modifier)) {
            throw new Exception("Modifier $modifier doesn't exist");
        }
        $modifierClass = $modifiersMap->get($modifier);
        $this->modifiers[$modifier] = new $modifierClass($this->domDocument);
        return $this->modifiers[$modifier];
    }
}
