<?php

namespace iHTML\iHTML;

use Exception;
use iHTML\Filesystem\FileDirectory;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use Illuminate\Support\Collection;
use Symfony\Component\Yaml\Yaml;

class Project
{
    private FileDirectoryExistent $directory;

    private Collection $resources;

    /**
     * @throws Exception
     */
    public function __construct(FileDirectoryExistent $directory)
    {
        $this->directory = $directory;
        $manifest = new FileRegularExistent("project.yaml", $this->directory);
        $manifest = (object)Yaml::parseFile($manifest);
        $this->resources = collect($manifest->resources)->map(
            fn($input, $output) => new ProjectRow($input, $output, $this->directory)
        )->values();
    }

    public function get(): Collection
    {
        return $this->resources;
    }

    /**
     * @throws Exception
     */
    public function render(FileDirectory $outputDir, string $index = null): void
    {
        $outputDir->create();
        $this->resources->map(function ($projectRow) use ($outputDir, $index) {
            /** @var ProjectRow $projectRow */
            $ccs = $projectRow->getCcs();
            $document = $projectRow->getDocument();
            $ccs->applyTo($document);
            $document->render();
            $document->save(
                $projectRow->getOutput(),
                $outputDir,
                ...($index ? [$index] : [])
            );
        });
    }
}
