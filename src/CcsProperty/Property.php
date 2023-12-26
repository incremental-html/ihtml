<?php

namespace iHTML\CcsProperty;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use Masterminds\HTML5;
use Symfony\Component\DomCrawler\Crawler;

abstract class Property
{
    const DISPLAY = 1001;
    const CONTENT = 1002;
    const NONE = 1005;
    const INHERIT = 1012;

    protected Crawler $domList;
    protected array $params;
    protected array $lates = [];

    public function __construct(
        protected DOMDocument $domDocument,
    )
    {
    }

    public static function ccsConstants(): array
    {
        return [
            'display' => Property::DISPLAY,
            'content' => Property::CONTENT,
            'none' => Property::NONE,
            'inherit' => Property::INHERIT,
        ];
    }

    abstract public function apply(DOMElement $element): void;

    public function render(): void
    {
    }

    public static function isValid(...$params): bool
    {
        return true;
    }

    public function setList(Crawler $list): void
    {
        $this->domList = $list;
    }

    public function __invoke(...$params): void
    {
        if (!$this->isValid(...$params)) {
            return;
        } // or throw new Exception

        $this->params = $params;

        foreach ($this->domList as $entry) {
            $this->apply($entry);
        }
    }

    protected static function solveParams(array $params, DOMNode $entry): string
    {
        $content = [];
        foreach ($params as $param) {
            switch (true) {
                case is_string($param):
                    $content[] = $param;
                    break;
                case $param === self::NONE:
                    // none
                    break;
                case $param === self::DISPLAY:
                    $content[] = $entry->ownerDocument->saveHTML($entry);
                    break;
                case $param === self::CONTENT:
                    foreach ($entry->childNodes as $childNode) {
                        $content[] = $entry->ownerDocument->saveHTML($childNode);
                    }
                    break;
//                case $param === self::TEXT:
                    // TODO
//                    break;
//                case $param instanceof ATTR and $param->value === self::CONTENT:
//                    $param = $entry->getAttribute($c->name);
//                    break;
                //case $param instanceof ATTR and $param->value === self::DISPLAY:
                // TODO
                //break;
                //case $param instanceof STYLE and $param->value === self::CONTENT:
                // TODO
                //break;
                //case $param instanceof STYLE and $param->value === self::DISPLAY:
                // TODO
                //break;
            }
        }
        return implode($content);
    }

    public function applyLater(DOMElement $element, $defaultValue): void
    {
        $attribute = $this->params[0];

        // addElementToHierarchy

        // if exists, removes it
        if (($key = Property::array_uSearch($element, $this->lates, function ($a, $b) {
                return $a->element === $b;
            })) !== false) {
            array_splice($this->lates, $key, 1);
        }

        // if not default, adds it
        if ($attribute != $defaultValue) {
            $this->lates[] = new Late($element, $attribute);
        }
    }

    public function latesExpandInherits()
    {
        $oldLates = $this->lates;

        // sorting by depth (asc)
        usort($oldLates, function ($a, $b) {
            return substr_count($a->element->getNodePath(), '/') - substr_count($b->element->getNodePath(), '/');
        });

        // expand
        $this->lates = [];
        foreach ($oldLates as $oldLate) {
            // expand single element (apply to all children the prop)
            foreach ((new Crawler($oldLate->element))->filter('*') as $childElement) {
                if (($key = Property::array_uSearch($childElement, $this->lates, function ($a, $b) {
                        return $a->element === $b;
                    })) !== false) {
                    array_splice($this->lates, $key, 1);
                }

                $this->lates[] = new Late($childElement, $oldLate->attribute);
            }
        }
    }

    private static function array_uSearch($needle, array $haystack, callable $callback): false|int|string
    {
        $res = array_filter($haystack, function ($var) use ($needle, $callback) {
            return $callback($var, $needle);
        });

        return array_keys($res)[0] ?? false;
    }

    protected function domFragment($content): DOMDocumentFragment
    {
        $fragment = $this->domDocument->createDocumentFragment();
        foreach (self::htmlToDOM($content, $this->domDocument) as $node) {
            $fragment->appendChild($node);
        }
        return $fragment;
    }

    private static function htmlToDOM($html, $doc): array
    {
        $html5parser = new HTML5();
        $wrapper = $html5parser
            ->loadHTML('<div id="html-to-dom-input-wrapper">' . $html . '</div>')
            ->getElementById('html-to-dom-input-wrapper')
        ;
        $children = array_map(
            fn($childNode) => $doc->importNode($childNode, true),
            iterator_to_array($wrapper->childNodes)
        );
        return $children;
    }
}

class Late
{
    public DOMElement $element;
    public $attribute;
    private int $weight;

    public function __construct(DOMElement $elem, $attr, int $weight = 0)
    {
        $this->element = $elem;
        $this->attribute = $attr;
        $this->weight = $weight;
    }
}
