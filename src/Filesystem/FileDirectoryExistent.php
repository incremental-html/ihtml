<?php
declare(strict_types=1);

namespace iHTML\Filesystem;

use Exception;

readonly class FileDirectoryExistent extends FileDirectory
{
    use FileExistent;

    public function copyTo(FileDirectory $outputDir): FileDirectoryExistent
    {
        $outputDir = $outputDir->create();
        exec(
            "rm -rf {$outputDir->getAbsolute()} ; cp -r {$this->getAbsolute()} {$outputDir->getAbsolute()}",
            $out,
            $exitCode,
        );
        if ($exitCode != 0) {
            throw new Exception("Copy failed. `cp` command exited with status code $exitCode");
        }
        return $outputDir;
    }
}
