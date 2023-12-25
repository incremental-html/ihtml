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
    private FileDirectoryExistent $root;

    private Collection $project;

    /**
     * @throws Exception
     */
    public function __construct(FileDirectoryExistent $projectDirectory)
    {
        $this->root = $projectDirectory;
        $projectFile = new FileRegularExistent("project.yaml", $this->root);
        $project = (object)Yaml::parseFile($projectFile);
        $this->project = collect($project)->map(
            fn($input, $output) => new ProjectRow($input, $output, $this->root)
        )->values();
    }

    public function get(): Collection
    {
        return $this->project;
    }

    /**
     * @throws Exception
     */
    public function render(FileDirectory $outputDir, string $index = null): void
    {
        $outputDir->create();
        $this->project->map(function ($projectRow) use ($outputDir, $index) {
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
