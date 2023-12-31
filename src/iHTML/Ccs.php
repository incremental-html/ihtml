<?php
declare(strict_types=1);

namespace iHTML\iHTML;

use Exception;
use iHTML\CcsParser\CcsDeclaration;
use iHTML\CcsParser\CcsParser;
use iHTML\CcsProperty\Property;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use function Symfony\Component\String\u;

readonly class Ccs
{
    public function __construct(
        protected string $code,
        protected FileDirectoryExistent $root,
    )
    {
    }

    public static function fromFile(FileRegularExistent $file): Ccs
    {
        return new self($file->contents(), $file->getPath());
    }

    public static function fromString(string $code, FileDirectoryExistent $root): self
    {
        return new self($code, $root);
    }

    public function getInheritance(): array
    {
        $inheritance = [];
        $parser = new CcsParser();
        $parser
            ->onImport(function (string $file) use (&$inheritance) {
                $ccs = Ccs::fromFile(new FileRegularExistent($file, $this->root));
                $imports = $ccs->getInheritance();
                $inheritance[$file] = array_merge($inheritance[$file] ?? [], $imports);
            })
        ;
        $parser->parse($this->code, $this->root);
        return $inheritance;
    }

    public function applyTo(Document $document): self
    {
        $parser = new CcsParser();
        $parser
            ->onImport(function (string $file) use ($document) {
                $ccs = Ccs::fromFile(new FileRegularExistent($file, $this->root));
                $ccs->applyTo($document);
            })
            ->onRule(function (array $selectors, array $declarations) use ($document) {
                $query = $document(implode(',', $selectors));
                if (!iterator_count($query)) {
                    return;
                }
                foreach ($declarations as $declaration) {
                    self::applyDeclaration($query, $declaration);
                }
            })
        ;
        $parser->parse($this->code, $this->root);
        return $this;
    }

    private static function applyDeclaration(
        DocumentQuery $query,
        CcsDeclaration $declaration,
    ): void
    {
        $method = self::getMethod($declaration);
        $arguments = self::getValues($declaration);
        $query->$method(...$arguments);
    }

    private static function getMethod(CcsDeclaration $declaration): string
    {
        return (string)u($declaration->property)->camel();
    }

    private static function getValues(CcsDeclaration $declaration): array
    {
        $propertyClass = '\\iHTML\\CcsProperty\\' . u($declaration->property)->camel()->title() . 'Property';
        if (!class_exists($propertyClass)) {
            throw new Exception("Class `$propertyClass` not implemented for property `$declaration->property`.");
        }
        /** @var Property $propertyClass */
        return $propertyClass::convertPropertyValues($declaration->values);
    }
}
