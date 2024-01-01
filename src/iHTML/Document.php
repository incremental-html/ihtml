<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use iHTML\DOM\DOMElement;
use Exception;
use iHTML\CcsProperty\Property;
use iHTML\DOM\DOMDocument;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use Masterminds\HTML5;
use Sabberworm\CSS\Parsing\SourceException;
use Symfony\Component\DomCrawler\Crawler;

class Document
{
    private HTML5 $parser;
    private DOMDocument $domDocument;
    private array $renders = [];

    /**
     * @throws SourceException
     * @throws Exception
     */
    public function __construct(FileRegularExistent $htmlFile)
    {
        $this->parser = new HTML5([
            'target_document' => new DOMDocument(),
            HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true,
        ]);
        $parsed = $this->parser->load($htmlFile);
        /** @var DOMDocument $parsed */
        $this->domDocument = $parsed;
        $this->ccsLinks($htmlFile);
        $this->ccsNodes($htmlFile);
        $this->ccsAttributes($htmlFile);
    }

    /**
     * Implements $document('SELECTOR')
     */
    public function __invoke($selector): DocumentQuery
    {
        $query = (new Crawler($this->domDocument))->filter($selector);
        return new DocumentQuery($this, $query);
    }

    // final rendering
    public function render(): void
    {
        foreach ($this->renders as $renderClass => $ignored) {
            /** @var Property $renderClass */
            $renderClass::render($this->domDocument);
        }
    }

    /**
     * @throws Exception
     */
    public function save(
        string $output,
        FileDirectoryExistent $outputDir,
        string $index = 'index.html',
    ): Document
    {
        $file = self::fileFromResource($output, $index, $outputDir);
        $file->getPath()->create();
        $this->render();
        $this->parser->save($this->domDocument, $file);
        return $this;
    }

    public function print(): Document
    {
        $this->render();
        print $this->parser->saveHTML($this->domDocument);
        return $this;
    }

    public function get(): string
    {
        $this->render();
        return $this->parser->saveHTML($this->domDocument);
    }

    /**
     * @throws Exception
     */
    private static function fileFromResource(string $output, string $index, FileDirectoryExistent $outputDir): FileRegular
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
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this('[content]') as $result) {
            /** @var DOMElement $result */
            // TODO: to develop
        }
    }

    public function appendRender(string $modifierClass): void
    {
        $this->renders[$modifierClass] = true;
    }
}
