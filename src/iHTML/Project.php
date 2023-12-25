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
            fn($input, $output) => new ProjectResource($input, $output, $this->directory)
        )->values();
    }

    /**
     * @throws Exception
     */
    public function render(FileDirectory $outputDir, string $index = null): void
    {
        $outputDir->create();
        $this->resources->map(function ($resource) use ($outputDir, $index) {
            /** @var ProjectResource $resource */
            $ccs = $resource->getCcs();
            $document = $resource->getDocument();
            $ccs->applyTo($document);
            $document->render();
            $document->save(
                $resource->getOutput(),
                $outputDir,
                ...($index ? [$index] : [])
            );
        });
    }

    public function get(): Collection
    {
        return $this->resources;
    }
}
