<?php

namespace iHTML\Messages;

use Message;
use SplFileInfo;

class File extends SplFileInfo implements Message
{
    private string $rawPath;
    public function __construct(string $file_name)
    {
        $this->rawPath = $file_name;
        parent::__construct($file_name);
    }
    public function __toString(): string
    {
        return $this->rawPath;
    }
}
