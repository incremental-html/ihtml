<?php


namespace iHTML\Ccs;

use Directory;
use Exception;
use iHTML\Document\Document;
use iHTML\Document\DocumentQueryAttribute;
use iHTML\Document\DocumentQueryClass;
use iHTML\Document\DocumentQueryStyle;
use iHTML\Messages\File;
use Sabberworm\CSS\Value\CSSString;
use Symfony\Component\Filesystem\Path;

abstract class Ccs
{
    protected string $code;
    protected Directory $root;

    private array $rules = [];
    private array $attrRules = [];
    private array $styleRules = [];
    private array $classRules = [];

    public function __construct()
    {
        $this->loadRules();
        $this->loadAttrRules();
        $this->loadStyleRules();
        $this->loadClassRules();
    }


    public function applyTo(Document $document): Ccs
    {
        $parser =
            (new CcsParser)
                ->setOnSelector(function (string $selectors, array $rules) use ($document) {
                    $query = $document($selectors);
                    if (!iterator_count($query)) {
                        return;
                    }
                    foreach ($rules as $rule) {
                        $ruleComponents = CcsRuleDecoder::decodeRule($this->rules, $rule->name);
                        $ruleType = $ruleComponents->type;
                        $ruleName = $ruleComponents->rule;
                        $ruleSubj = $ruleComponents->name;
                        switch ($ruleType) {
                            case 'node':
                                ('\\iHTML\\Ccs\\Rules\\' . $this->rules[$ruleName])::exec($query, $rule->values, $rule->content);
                                break;
                            case 'attr':
                                $this->attrRules[$ruleName]($query, $ruleSubj, $rule->values, $rule->content);
                                break;
                            case 'style':
                                $this->styleRules[$ruleName]($query, $ruleSubj, $rule->values, $rule->content);
                                break;
                            case 'class':
                                $this->classRules[$ruleName]($query, $ruleSubj, $rule->values, $rule->content);
                                break;
                            default:
                                throw new Exception("Rule type {$ruleType} not defined.");
                                break;
                        }
                    }
                })
                ->setOnImport(function (string $file) use ($document) {
                    $ccs = new CcsFile(new File(Path::makeAbsolute($file, $this->root->path)));
                    $ccs->applyTo($document);
                })
                ->parse($this->code, $this->root);
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


    private function loadRules()
    {
        $this->rules =
            // scans rules directory
            collect(scandir(__DIR__ . '/Rules'))
                ->diff(['.', '..'])
                // gets class name from filename
                ->map(fn($file) => Path::getFilenameWithoutExtension($file))
                // maps in form of [ rule name => class ]
                ->mapWithKeys(fn($rule) => [('\\iHTML\\Ccs\\Rules\\' . $rule)::rule() => $rule]);
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
