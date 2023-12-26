<?php


namespace iHTML\iHTML;

use Exception;
use iHTML\CcsParser\CcsDeclaration;
use iHTML\CcsParser\CcsParser;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Value\CSSString;
use function Symfony\Component\String\u;

class Ccs
{
    protected string $code;
    protected FileDirectoryExistent $root;

    /**
     * @throws Exception
     */
    public static function fromFile(FileRegularExistent $file): Ccs
    {
        return new self($file->contents(), $file->getPath());
    }

    public static function fromString(string $code, FileDirectoryExistent $root): self
    {
        return new self($code, $root);
    }

    public function __construct(string $code, FileDirectoryExistent $root)
    {
        $this->code = $code;
        $this->root = $root;
    }

    /**
     * @throws SourceException
     */
    public function applyTo(Document $document): self
    {
        $parser = new CcsParser;
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
                    /** @var CcsDeclaration $declaration */
                    $this->declarationApply($declaration, $query);
                }
            })
        ;
        $parser->parse($this->code, $this->root);
        return $this;
    }

    /**
     * @return array
     * @throws SourceException
     */
    public function getInheritance(): array
    {
        $inheritance = [];
        $parser = new CcsParser;
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

//    private function solveValues($values, array $constants = [])
//    {
//        return array_map(
//        /**
//         * @throws Exception
//         */
//            function ($value) use ($constants) {
//                if ($value instanceof CssString) {
//                    return $value->getString();
//                } elseif (is_string($value) && isset($constants[$value])) {
//                    return $constants[$value];
//                } else {
//                    throw new Exception("$value unrecognized");
//                }
//            }, $values);
//    }

    /**
     * @throws Exception
     */
    private function declarationApply(CcsDeclaration $declaration, DocumentQuery $query): void
    {
        $property = $declaration->property;
        $method = (string)u($property)->camel();
        $methodClass = '\\iHTML\\CcsProperty\\' . u($method)->title() . 'Property';
        if (!class_exists($methodClass)) {
            throw new Exception("Class `$methodClass` not implemented for method `$method`.");
        }
        $values = collect($declaration->values)
            ->map(fn($value) => match (true) {
                $value instanceof CSSString => $value->getString(),
                is_string($value) => $methodClass::constants()[$value] ?? throw new Exception("Constant `$value` not defined."),
                default => throw new Exception("Value $value not recognized."),
            })
        ;
        $query->$method(...$values);
    }
}
