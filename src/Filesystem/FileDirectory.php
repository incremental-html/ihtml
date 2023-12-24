<?php

namespace iHTML\Filesystem;

use Exception;

class FileDirectory extends File
{
    public function __construct(string $filename, ?string $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        if (file_exists($this->getAbsolute()) && !$this->info->isDir()) {
            throw new Exception("File $this->path is not a directory.");
        }
    }

    /**
     * @throws Exception
     */
    public function create(): void
    {
        if (file_exists($this)) {
            return;
        }
        if (!mkdir($this, 0777, true)) {
            throw new Exception("Error creating $this directory.");
        }
    }
}
