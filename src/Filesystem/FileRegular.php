<?php

namespace iHTML\Filesystem;

use Exception;
use SplFileObject;

class FileRegular extends File
{
    protected SplFileObject $object;

    /**
     * @throws Exception
     */
    public function __construct(string $filename, ?string $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        if (file_exists($this) && !$this->info->isFile()) {
            throw new Exception("File $this->path is not a regular file.");
        }
    }

    public function contents(): ?string
    {
        $contents = file_get_contents($this);
        return $contents === false ?
            null :
            $contents;
    }
}