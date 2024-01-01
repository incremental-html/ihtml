<?php
declare(strict_types=1);

namespace iHTML\DOM;

use DOMDocumentFragment as PHPDOMDocumentFragment;
use Masterminds\HTML5;

class DOMDocumentFragment extends PHPDOMDocumentFragment
{
    public function fromString(string $content): void
    {
        foreach (self::htmlToDOM($content, $this->document()) as $node) {
            $this->appendChild($node);
        }
    }

    public static function htmlToDOM(string $html, DOMDocument $doc): array
    {
        $html5parser = new HTML5(); // DOMDocument::parser();
        $nodes = $html5parser
            ->loadHTML('<div id="html-to-dom-input-wrapper">' . $html . '</div>')
            ->getElementById('html-to-dom-input-wrapper')
            ->childNodes;
        /** @var array $children */
        $children = array_map(
            fn($childNode) => $doc->importNode($childNode, true),
            iterator_to_array($nodes),
        );
        return $children;
    }

    public function document(): DOMDocument
    {
        /** @var DOMDocument {$this->ownerDocument} */
        return $this->ownerDocument;
    }
}