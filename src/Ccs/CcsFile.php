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

class CcsFile extends CcsHandler
{
    public function __construct(SplFileObject $file)
    {
        parent::__construct();
        $this->code = $file->fread($file->getSize() + 1);
        $this->root = dir($file->getPath());
    }
}
