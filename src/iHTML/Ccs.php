<?php


namespace iHTML\iHTML;

use Exception;
use iHTML\CcsParser\CcsParser;
use iHTML\CcsParser\CcsPropertyDecoder;
use iHTML\Document\DocumentQueryAttribute;
use iHTML\Document\DocumentQueryClass;
use iHTML\Document\DocumentQueryStyle;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use Sabberworm\CSS\Value\CSSString;
use Symfony\Component\Filesystem\Path;

class Ccs
{
    protected string $code;
    protected FileDirectoryExistent $root;

    private array $properties = [];
    private array $attrRules = [];
    private array $styleRules = [];
    private array $classRules = [];

    public static function fromChunk(string $code, FileDirectoryExistent $root): self
    {
        return new self($code, $root);
    }

    /**
     * @throws Exception
     */
    public static function fromFile(FileRegularExistent $file): Ccs
    {
        return new self($file->contents(), $file->getPath());
    }

    public function __construct(string $code, FileDirectoryExistent $root)
    {
        $this->loadProperties();
        $this->loadAttrRules();
        $this->loadStyleRules();
        $this->loadClassRules();

        $this->code = $code;
        $this->root = $root;
    }

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
                    $ruleComponents = CcsPropertyDecoder::decodeDeclaration($this->properties, $declaration->name);
                    $ruleType = $ruleComponents->type;
                    $ruleName = $ruleComponents->rule;
                    $ruleSubj = $ruleComponents->name;
                    switch ($ruleType) {
                        case 'node':
                            ('\\iHTML\\CcsProperty\\' . $this->properties[$ruleName])::exec($query, $declaration->values, $declaration->content);
                            break;
                        case 'attr':
                            $this->attrRules[$ruleName]($query, $ruleSubj, $declaration->values, $declaration->content);
                            break;
                        case 'style':
                            $this->styleRules[$ruleName]($query, $ruleSubj, $declaration->values, $declaration->content);
                            break;
                        case 'class':
                            $this->classRules[$ruleName]($query, $ruleSubj, $declaration->values, $declaration->content);
                            break;
                        default:
                            throw new Exception("Rule type $ruleType not defined.");
                    }
                }
            })
        ;
        $parser->parse($this->code, $this->root);
        return $this;
    }


    public function getHierarchyList(): array
    {
        if ($this->file) {
            $parser = new CcsParser;
            return $parser->inheritanceFile($this->file, CcsParser::INHERITANCE_LIST);
        } elseif ($this->code) {
            $parser = new CcsParser;
            return $parser->inheritanceCode($this->code, CcsParser::INHERITANCE_LIST);
        } else {
            throw new Exception('Ccs: code or file not set');
        }
    }


    public function getHierarchyTree(): array
    {
        if ($this->file) {
            $parser = new CcsParser;
            return $parser->inheritanceFile($this->file, CcsParser::INHERITANCE_TREE);
        } elseif ($this->code) {
            $parser = new CcsParser;
            return $parser->inheritanceCode($this->code, CcsParser::INHERITANCE_TREE);
        } else {
            throw new Exception('Ccs: code or file not set');
        }
    }


    private function loadProperties()
    {
        $this->properties =
            // scans rules directory
            collect(scandir(__DIR__ . '/../CcsProperty'))
                ->diff(['.', '..', 'Property.php'])
                // gets class name from filename
                ->map(fn($file) => Path::getFilenameWithoutExtension($file))
                // maps in form of [ property name => class ]
                ->mapWithKeys(fn($property) => [
                    ('\\iHTML\\CcsProperty\\' . $property)::property() => $property,
                ])
                ->toArray();
    }

    private function loadAttrRules()
    {
        $this->attrRules = [
            'content' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values);
                $query->attr($name)->content(...$values);
            },
            'display' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values);
                $query->attr($name)->display(...$values);
            },
            'visibility' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['visible' => DocumentQueryAttribute::VISIBLE, 'hidden' => DocumentQueryAttribute::HIDDEN]);
                $query->attr($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function loadStyleRules()
    {
        $this->styleRules = [
            'content' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values);
                $query->style($name)->content(...$values);
            },
            'literal' => function ($query, $name, $values, $content) {
                $query->style($name)->content($content);
            },
            'display' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['none' => DocumentQueryStyle::NONE]);
                $query->style($name)->display(...$values);
            },
            'visibility' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['visible' => DocumentQueryStyle::VISIBLE, 'hidden' => DocumentQueryStyle::HIDDEN]);
                $query->style($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function loadClassRules()
    {
        $this->classRules = [
            'visibility' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['visible' => DocumentQueryClass::VISIBLE, 'hidden' => DocumentQueryClass::HIDDEN]);
                $query->className($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function solveValues($values, array $constants = [])
    {
        return array_map(function ($value) use ($constants) {
            if ($value instanceof CssString) {
                return $value->getString();
            } elseif (is_string($value) && isset($constants[$value])) {
                return $constants[$value];
            } else {
                throw new Exception("$value unrecognized");
            }
        }, $values);
    }
}
