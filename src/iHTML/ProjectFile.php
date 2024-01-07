<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use iHTML\Filesystem\FileRegularExistent;
use Symfony\Component\Yaml\Yaml;

readonly class ProjectFile
{
    private string $static;
    private string $index;
    /**
     * A map in form of: address => resource
     * @var array<string, array{string, string}>
     */
    private array $resources;
    /**
     * A map in form of: 404 => resource
     * @var array<int, array{string, string}>
     */
    private array $errors;

    public function __construct(FileRegularExistent $yamlParsed)
    {
        $yamlParsed = Yaml::parseFile((string)$yamlParsed);
        $this->static = $yamlParsed['static'];
        $this->index = $yamlParsed['index'];
        $this->resources = $yamlParsed['resources'];
        $this->errors = $yamlParsed['errors'];
    }

    public function getStatic(): string
    {
        return $this->static;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}