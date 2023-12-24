<?php

namespace iHTML\Filesystem;

use Exception;

class FileDirectoryExistent extends FileDirectory
{
    public function __construct(string $filename, ?string $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        $this->mustExist();
    }
}
