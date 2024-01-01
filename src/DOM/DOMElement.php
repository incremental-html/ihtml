<?php
declare(strict_types=1);

namespace iHTML\DOM;

use DOMElement as PHPDOMElement;

class DOMElement extends PHPDOMElement
{
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