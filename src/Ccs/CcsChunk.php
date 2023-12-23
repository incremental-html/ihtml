<?php


namespace iHTML\Ccs;

use CcsRuleDecoder;
use Directory;
use iHTML\Document\DocumentQueryAttr;

class CcsChunk extends Ccs
{
    public function __construct(string $code, Directory $root)
    {
        parent::__construct();
        $this->code = $code;
        $this->root = $root;
    }
}
