<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use Exception;
use iHTML\CcsProperty\Property;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use Sabberworm\CSS\Parsing\SourceException;
use Symfony\Component\DomCrawler\Crawler;

class Document
{
    private DOMDocument $domDocument;
    private FileDirectoryExistent $workingDir;
    private array $renders = [];

    /**
     * @throws SourceException
     * @throws Exception
     */
    public function __construct(FileRegularExistent $htmlFile)
    {
        $parsed = DOMDocument::fromFile($htmlFile);
        $this->domDocument = $parsed;
        $this->workingDir = $htmlFile->getPath();
        $this->ccsLinks($htmlFile);
        $this->ccsNodes($htmlFile);
        $this->ccsAttributes($htmlFile);
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

    // final rendering

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

    /**
     * Implements $document('SELECTOR')
     */
    public function __invoke($selector, ?FileDirectoryExistent $workingDir = null): DocumentQuery
    {
        $query = (new Crawler($this->domDocument))->filter($selector);
        return new DocumentQuery($this, $workingDir ?? $this->workingDir, $query);
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
        $this->domDocument->asFile($file);
        return $this;
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

    public function render(): void
    {
        foreach ($this->renders as $renderClass => $ignored) {
            /** @var Property $renderClass */
            $renderClass::render($this->domDocument);
        }
    }

    public function print(): Document
    {
        $this->render();
        print $this->domDocument->asString();
        return $this;
    }

    public function get(): string
    {
        $this->render();
        return $this->domDocument->asString();
    }

    public function appendRender(string $modifierClass): void
    {
        $this->renders[$modifierClass] = true;
    }

}
