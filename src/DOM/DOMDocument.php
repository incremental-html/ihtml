<?php
declare(strict_types=1);

namespace iHTML\DOM;

use DOMDocument as PHPDOMDocument;
use DOMElement as PHPDOMElement;

class DOMDocument extends PHPDOMDocument
{
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->registerNodeClass(PHPDOMElement::class, DOMElement::class);
    }
}