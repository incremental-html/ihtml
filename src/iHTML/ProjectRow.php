<?php

namespace iHTML\iHTML;

use Exception;
use iHTML\Filesystem\FileRegularExistent;

class ProjectRow
{
    private string $html;
    private string $apply;
    private string $output;
    private Document $document;
    private Ccs $ccs;

    /**
     * @param array $input
     * @param string $output
     * @param string $wd
     * @throws Exception
     */
    public function __construct(array $input, string $output, string $wd)
    {
        [$this->html, $this->apply] = $input;
        $this->output = $output;
        $this->document = new Document(new FileRegularExistent($this->html, $wd));
        $this->ccs = Ccs::fromFile(new FileRegularExistent($this->apply, $wd));
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getApply(): string
    {
        return $this->apply;
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