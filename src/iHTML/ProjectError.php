<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use Exception;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;

readonly class ProjectError
{
    private string $html;
    private string $apply;
    private int $code;
    private Document $document;
    private Ccs $ccs;

    /**
     * @throws Exception
     */
    public function __construct(array $input, int $code, FileDirectoryExistent $wd)
    {
        [$this->html, $this->apply] = $input;
        $this->code = $code;
        $this->document = new Document(new FileRegularExistent($this->html, $wd));
        $this->ccs = Ccs::fromFile(new FileRegularExistent($this->apply, $wd));
    }

    public function getCode(): int
    {
        return $this->code;
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