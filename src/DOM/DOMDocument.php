<?php
declare(strict_types=1);

namespace iHTML\DOM;

use DOMDocument as PHPDOMDocument;
use DOMDocumentFragment as PHPDOMDocumentFragment;
use DOMElement as PHPDOMElement;
use iHTML\Filesystem\FileRegular;
use iHTML\Filesystem\FileRegularExistent;
use Masterminds\HTML5;

class DOMDocument extends PHPDOMDocument
{
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->registerNodeClass(PHPDOMElement::class, DOMElement::class);
        $this->registerNodeClass(PHPDOMDocumentFragment::class, DOMDocumentFragment::class);
    }

    public static function fromFile(FileRegularExistent $htmlFile): DOMDocument
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $document = DOMDocument::parser()->load($htmlFile);
        /** @var DOMDocument $document */
        return $document;
    }

    /**
     * @return HTML5
     */
    public static function parser(): HTML5
    {
        return new HTML5([
            'target_document' => new DOMDocument(),
            HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true,
        ]);
    }

    public function asString(): string
    {
        return self::parser()->saveHTML($this);
    }

    public function asFile(FileRegular $file): void
    {
        self::parser()->save($this, $file);
    }

    public function fragmentFromString(string $content): DOMDocumentFragment
    {
        /** @var DOMDocumentFragment $fragment */
        $fragment = $this->createDocumentFragment();
        $fragment->fromString($content);
        return $fragment;
    }
}