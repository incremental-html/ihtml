<?php


namespace iHTML\Ccs;

use iHTML\Document\Document;
use iHTML\Document\DocumentQueryAttr;
use iHTML\Document\DocumentQueryClass;
use iHTML\Document\DocumentQueryStyle;
use Exception;
use SplFileObject;
use Directory;
use danog\ClassFinder\ClassFinder;
use CcsRuleDecoder;

class CcsChunk extends CcsHandler
{
    public function __construct(string $code, Directory $root)
    {
        parent::__construct();
        $this->code = $code;
        $this->root = $root;
    }
}
