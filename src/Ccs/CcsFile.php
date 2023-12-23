<?php


namespace iHTML\Ccs;

use iHTML\Messages\File;

class CcsFile extends Ccs
{
    public function __construct(File $file)
    {
        parent::__construct();
        $this->code = $file->fread($file->getSize() + 1);
        $this->root = dir($file->getPath());
    }
}
