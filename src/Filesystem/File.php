<?php
declare(strict_types=1);

namespace iHTML\Filesystem;

use Exception;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;

/**
 * Class File
 * @package iHTML\Filesystem
 *
 * @see https://www.scaler.com/topics/file-type-in-linux/
 */
abstract class File
{
    protected string $path;
    protected ?FileDirectoryExistent $workingDir;
    protected SplFileInfo $info;

    /**
     * @throws Exception
     */
    public function __construct(string $filename, ?FileDirectoryExistent $workingDir = null)
    {
        if (Path::isRelative($filename) && !$workingDir) {
            throw new Exception("Relative path $filename without working dir is not allowed.");
        }
        $this->path = $filename;
        $this->workingDir = $workingDir;
        $this->info = new SplFileInfo($this->getAbsolute());
    }

    public function __toString(): string
    {
        return $this->getAbsolute();
    }

    /**
     * @throws Exception
     */
    protected function mustExist(): void
    {
        if (!file_exists($this->getAbsolute())) {
            throw new Exception("$this does not exist.");
        }
    }

    public function getAbsolute(): string
    {
        return Path::isAbsolute($this->path) ?
            $this->path :
            Path::makeAbsolute($this->path, $this->workingDir->getAbsolute());
    }

    /**
     * @throws Exception
     */
    public function getPath(): FileDirectory
    {
        return new FileDirectory($this->info->getPath());
    }
}
