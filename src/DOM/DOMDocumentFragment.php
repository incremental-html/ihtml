<?php
declare(strict_types=1);

namespace iHTML\DOM;

use DOMDocumentFragment as PHPDOMDocumentFragment;

class DOMDocumentFragment extends PHPDOMDocumentFragment
{
    public function fromString(string $content): void
    {
        var_dump($content);
        foreach (self::htmlToDOM($content, $this->document()) as $node) {
            $this->appendChild($node);
        }
    }

    public static function htmlToDOM(string $html, DOMDocument $doc): array
    {
        $html5parser = DOMDocument::parser();
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

    public function document(): DOMDocument
    {
        /** @var DOMDocument {$this->ownerDocument} */
        return $this->ownerDocument;
    }
}