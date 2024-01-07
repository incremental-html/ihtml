<?php
declare(strict_types=1);

namespace iHTML\Filesystem;

use Exception;

readonly class FileDirectory extends File
{
    public function __construct(string $filename, ?FileDirectory $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        if (file_exists($this->getAbsolute()) && !$this->info->isDir()) {
            throw new Exception("File $this->path is not a directory.");
        }
    }

    /**
     * @throws Exception
     */
    public function create(): FileDirectoryExistent
    {
        if (file_exists($this->getAbsolute())) {
            return new FileDirectoryExistent($this->path, $this->workingDir);
        }
        if (!mkdir($this->getAbsolute(), 0777, true)) {
            throw new Exception("Error creating $this directory.");
        }
        return new FileDirectoryExistent($this->path, $this->workingDir);
    }
}
