<?php

namespace RegexParser\Parser\Node;

use RegexParser\Parser\AbstractNode;

class BlockNode extends AbstractNode
{
    protected $isSubPattern;

    public function __construct(array $childNodes, $isSubPattern = false) {
        parent::__construct('block', null, $childNodes);
        $this->isSubPattern = $isSubPattern;
    }

    public function isSubPattern()
    {
        return $this->isSubPattern;
    }
}
