<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use Exception;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;

readonly class ProjectResource
{
    private string $html;
    private string $apply;
    private string $output;
    private Document $document;
    private Ccs $ccs;

    /**
     * @throws Exception
     */
    public function __construct(array $input, string $output, FileDirectoryExistent $wd)
    {
        [$this->html, $this->apply] = $input;
        $this->output = $output;
        $this->document = new Document(new FileRegularExistent($this->html, $wd));
        $this->ccs = Ccs::fromFile(new FileRegularExistent($this->apply, $wd));
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getCcs(): Ccs
    {
        return $this->ccs;
    }
}