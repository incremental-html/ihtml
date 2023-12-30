<?php
declare(strict_types=1);

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

    /**
     * @param Crawler $list
     * @param array $params
     * @return void
     */
    abstract public static function apply(Crawler $list, array $params): void;

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
            }
        }
        return implode($content);
    }

    public static function applyLater(Crawler $list, $attribute, $defaultValue): array
    {
        $laterList = [];
        foreach ($list as $element) {
            $laterList = array_filter(
                $laterList,
                fn($var) => $var->element !== $element,
            );
            if ($attribute != $defaultValue) {
                $laterList[] = new Late($element, $attribute);
            }
        }
        return $laterList;
    }

    public static function laterExpandInherits($laterList): array
    {
        // sorting by depth (asc)
        usort(
            $laterList,
            fn($a, $b) =>
                substr_count($a->element->getNodePath(), '/') -
                substr_count($b->element->getNodePath(), '/')
        );
        // expand
        $newLateList = [];
        foreach ($laterList as $late) {
            // expand single element (apply property to all children)
            foreach ((new Crawler($late->element))->filter('*') as $childElement) {
                $newLateList = array_filter($newLateList, fn($var) => $var->element !== $childElement);
                $newLateList[] = new Late($childElement, $late->attribute);
            }
        }
        return $newLateList;
    }

    protected static function domFragment($content, DOMDocument $domDocument): DOMDocumentFragment
    {
        $fragment = $domDocument->createDocumentFragment();
        foreach (self::htmlToDOM($content, $domDocument) as $node) {
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
        /** @var array $children */
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
    public mixed $attribute;
    private int $weight;

    public function __construct(DOMElement $elem, $attr, int $weight = 0)
    {
        $this->element = $elem;
        $this->attribute = $attr;
        $this->weight = $weight;
    }
}
