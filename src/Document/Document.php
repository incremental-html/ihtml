<?php

namespace iHTML\Document;

use Masterminds\HTML5;
use iHTML\Ccs\Ccs;
use Exception;
use danog\ClassFinder\ClassFinder;
use DOMDocument;
use Symfony\Component\DomCrawler\Crawler;
use SplFileInfo;
use SplFileObject;
use iHTML\Messages\IhtmlFile;
use Webmozart\PathUtil\Path;

class Document
{
    private DOMDocument $domdocument;
    private array $modifiers = [];

    public function __construct(SplFileObject $html)
    {
        $this->domdocument = (new HTML5)->load($html->getPathname(), [ HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true ]);
        // LOAD INTERNAL CCS
        // <link rel="contentsheet" href="..."> ...
        foreach ($this('link[rel="contentsheet"][href]') as $result) {
            $ccs = new Ccs(Path::makeAbsolute($result->getAttribute('href'), $html->getPath()));
            $ccs->applyTo($this);
            $result->parentNode->removeChild($result);
        }
        // <content> ... </content> ...
        foreach ($this('content') as $result) {
            $ccs = new Ccs();
            $ccs->setContent($result->textContent, $html->getPath());
            $ccs->applyTo($this);
            $result->parentNode->removeChild($result);
        }
        // <ELEM content="..."> ...
        foreach ($this('[content]') as $result) {
            // TODO
        }
    }


    // implements $document('SELECTOR') ...
    public function __invoke($selector)
    {
        $query = ( new Crawler($this->domdocument) )->filter($selector);
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

    public function save(IhtmlFile $output, string $index = "index.html"): Document
    {
        if (substr($output, -1) == '/') {
            $output = new SplFileInfo($output . $index);
        }
        if (!empty($output->getPath()) && !file_exists($output->getPath())) {
            mkdir($output->getPath(), 0777, true);
        }
        (new HTML5)->save($this->domdocument, $output);
        return $this;
    }

    public function print(): Document
    {
        print (new HTML5)->saveHTML($this->domdocument);
        return $this;
    }

    public function get(): string
    {
        return (new HTML5)->saveHTML($this->domdocument);
    }

    public function getModifier(string $modifier)
    {
        // modifiersMap maps modifiers method with classes, in form of: [ method => class, ... ]
        $modifiersMap =
            collect( scandir( __DIR__.'/Modifiers') )
            ->diff( ['.', '..'] )
            ->map( fn ($file) => '\\iHTML\\Document\\Modifiers\\'.Path::getFilenameWithoutExtension($file) )
            ->mapWithKeys( fn ($modifierClass) => [ $modifierClass::queryMethod() => $modifierClass ] );
        if (!$modifiersMap->has($modifier)) {
            throw new Exception("Modifier $modifier doesn't exist");
        }
        $modifierClass = $modifiersMap->get($modifier);
        $this->modifiers[ $modifier ] = new $modifierClass($this->domdocument);
        return $this->modifiers[ $modifier ];
    }
}
