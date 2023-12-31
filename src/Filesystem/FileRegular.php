<?php
declare(strict_types=1);

namespace iHTML\Filesystem;

use Exception;
use SplFileObject;

readonly class FileRegular extends File
{
    protected SplFileObject $object;

    /**
     * @throws Exception
     */
    public function __construct(string $filename, ?FileDirectoryExistent $workingDir = null)
    {
        parent::__construct($filename, $workingDir);
        if (file_exists($this->getAbsolute()) && !$this->info->isFile()) {
            throw new Exception("File $this->path is not a regular file.");
        }
    }

    public function getContents(): ?string
    {
        $contents = file_get_contents($this->getAbsolute());
        return $contents === false ?
            null :
            $contents;
    }

    public function putContents(string $contents): void
    {
        $return = file_put_contents($this->getAbsolute(), $contents);
        if ($return === false) {
            throw new Exception("Failed to write to file {$this->getAbsolute()} (content length: " . strlen($contents) . ')');
        }
    }
}
