<?php
declare(strict_types=1);

namespace iHTML\DOM;

use DOMElement as PHPDOMElement;

class DOMElement extends PHPDOMElement
{
    public function empty(): void
    {
        while ($this->hasChildNodes()) {
            $this->removeChild($this->firstChild);
        }
    }

    public function appendContent(string $content): void
    {
        $fragment = $this->document()->fragmentFromString($content);
        $this->appendChild($fragment);
    }

    public function document(): DOMDocument
    {
        /** @var DOMDocument {$this->ownerDocument} */
        return $this->ownerDocument;
    }

    public function content(): string
    {
        return collect($this->childNodes)
            ->map(fn($n) => $this->ownerDocument->saveHTML($n))
            ->join('')
        ;
    }

    public function display(): string
    {
        return $this->ownerDocument->saveHTML($this);
    }
}