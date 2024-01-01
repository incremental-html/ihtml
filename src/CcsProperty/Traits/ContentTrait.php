<?php
declare(strict_types=1);

namespace iHTML\CcsProperty\Traits;

use DOMDocumentFragment;
use iHTML\DOM\DOMElement;
use iHTML\DOM\DOMDocument;
use Masterminds\HTML5;

trait ContentTrait
{
    protected static function solveParams(array $params, DOMElement $entry): string
    {
        $content = [];
        foreach ($params as $param) {
            $content[] = match (true) {
                is_string($param) => $param,
                is_callable($param) => $param($entry),
            };
        }
        return implode($content);
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
            iterator_to_array($wrapper->childNodes),
        );
        return $children;
    }
}