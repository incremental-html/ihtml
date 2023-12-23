<?php

namespace iHTML\Project;

use Directory;
use Exception;
use iHTML\Ccs\CcsFile;
use iHTML\Document\Document;
use iHTML\Messages\File;
use Illuminate\Support\Collection;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

class Project
{
    private $root;

    private Collection $project;

    public function __construct(Directory $project)
    {
        $this->root = Path::makeAbsolute($project->path, getcwd());
        if (!file_exists("{$this->root}/project.yaml")) {
            throw new Exception("Project file {$this->root}/project.yaml not found.");
        }
        $project = (object)Yaml::parseFile("{$this->root}/project.yaml");
        if (!is_object($project)) {
            throw new Exception("Malformed project file {$this->root}/project.yaml.");
        }
        $this->project = collect($project)
            ->map(
                fn($a, $output) => (object)[
                    'document' => new Document(new File(Path::makeAbsolute($a[0], $this->root))),
                    'ccs' => new CcsFile(new File(Path::makeAbsolute($a[1], $this->root))),
                    'html' => $a[0],
                    'apply' => $a[1],
                    'output' => $output,
                ]
            );
    }

    public function get()
    {
        return $this->project;
    }

    public function render(SplFileInfo $out_dir, string $index = null)
    {
        $this->createDir($out_dir);
        if (!$out_dir->isDir()) {
            throw new Exception('Error creating output directory.');
        }
        if (!$out_dir->isWritable()) {
            throw new Exception('Error creating output directory.');
        }
        // COMPILE ALL FILES
        $this->project->map(
            function ($res) use ($out_dir, $index) {
                $res->ccs->applyTo($res->document);
                $res->document->render();
                $res->document->save(new File(Path::makeAbsolute($res->output ?: './', (string)$out_dir)), ...($index ? [$index] : []));
            }
        );
    }

    private function createDir(SplFileInfo $dir)
    {
        if (file_exists($dir)) {
            return;
        }
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Error creating {$dir} directory.");
        }
    }
}
