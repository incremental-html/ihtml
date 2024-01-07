<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use Exception;
use iHTML\Filesystem\FileDirectory;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use Illuminate\Support\Collection;

readonly class Project
{
    private FileDirectoryExistent $directory;

    private FileDirectoryExistent $static;
    private string $index;
    private Collection $resources;
    private Collection $errors;

    /**
     * @throws Exception
     */
    public function __construct(FileDirectoryExistent $directory)
    {
        $this->directory = $directory;
        $projectFile = new FileRegularExistent('project.yaml', $this->directory);
        $project = new ProjectFile($projectFile);

        $this->static = new FileDirectoryExistent($project->getStatic(), $this->directory);
        $this->index = $project->getIndex();
        $this->resources = collect($project->getResources())->map(
            fn($input, $output) => new ProjectResource($input, $output, $this->directory),
        )->values();
        $this->errors = collect($project->getErrors())->map(
            fn($input, $output) => new ProjectError($input, $output, $this->directory),
        )->values();
    }

    /**
     * @throws Exception
     */
    public function render(FileDirectory $outputDir): void
    {
        // Copy static files
        $outputDir = $this->static->copyTo($outputDir);

        // Create resources
        $this->resources->map(function (ProjectResource $resource) use ($outputDir) {
            $ccs = $resource->getCcs();
            $document = $resource->getDocument();
            $ccs->applyTo($document);
            $document->save(
                $resource->getOutput(),
                $outputDir,
                ...($this->index ? [$this->index] : []),
            );
        });

        // Create errors
        $errorDirName = '.errors';
        $errorDir = new FileDirectory($errorDirName, $outputDir);
        $errorDir = $errorDir->create();
        $this->errors->map(function (ProjectError $error) use ($errorDir) {
            $ccs = $error->getCcs();
            $document = $error->getDocument();
            $ccs->applyTo($document);
            $document->save(
                "{$error->getCode()}.html",
                $errorDir,
            );
        });

        // Create .htaccess file (only for Apache)
        $htaccessContent = $this->errors->map(
            fn(ProjectError $error) => "ErrorDocument {$error->getCode()} /$errorDirName/{$error->getCode()}.html",
        )->join("\n");
        $htaccessFile = new FileRegular('.htaccess', $outputDir);
        $htaccessFile->putContents($htaccessContent);
    }

    public function get(): Collection
    {
        return $this->resources;
    }
}
