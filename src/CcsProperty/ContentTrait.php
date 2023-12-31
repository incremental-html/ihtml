<?php

namespace iHTML\CcsProperty;

use DOMDocument;
use DOMDocumentFragment;
use Masterminds\HTML5;

trait ContentTrait
{
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