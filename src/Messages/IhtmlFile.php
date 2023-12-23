<?php

namespace iHTML\Messages;

use SplFileInfo;

class IhtmlFile extends SplFileInfo
{
    private string $rawpath;
    public function __construct(string $file_name)
    {
        $this->rawpath = $file_name;
        parent::__construct($file_name);
    }
    public function __toString()
    {
        return $this->rawpath;
    }
}
