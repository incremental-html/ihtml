<?php

namespace iHTML\Filesystem;

use Exception;

class FileRegularExistent extends FileRegular
{
    /**
     * @throws Exception
     */
    public function __construct(string $filename, ?string $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        $this->mustExist();
    }
}
