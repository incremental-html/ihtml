<?php

namespace iHTML\Filesystem;

use Exception;

trait FileExistent
{
    public function __construct(string $filename, ?string $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        $this->mustExist();
    }

    /**
     * @throws Exception
     */
    public function getPath(): FileDirectoryExistent
    {
        return new FileDirectoryExistent($this->info->getPath());
    }
}