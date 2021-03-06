<?php

namespace RegexParser\Generator;

use RegexParser\AbstractGenerator;
use RegexParser\Lexer\EscapeToken;
use RegexParser\Parser\Node\AlternativeNode;
use RegexParser\Parser\Node\BeginNode;
use RegexParser\Parser\Node\BlockNode;
use RegexParser\Parser\Node\CharacterClassNode;
use RegexParser\Parser\Node\EndNode;
use RegexParser\Parser\Node\RepetitionNode;
use RegexParser\Parser\Node\TokenNode;
use RegexParser\Parser\Parser;

class RandomGenerator extends AbstractGenerator
{
    /**
     * @param string $pattern
     *
     * @return RandomGenerator
     */
    public static function create($pattern)
    {
        $parser = Parser::create();

        return new self($parser->parse($pattern));
    }

    /**
     * @param int $seed
     *
     * @return string
     */
    public function generate($seed = null)
    {
        if ($seed !== null) {
            mt_srand($seed);
        }

        $output = '';

        foreach ($this->ast->getChildNodes() as $childNode) {
            $output .= $this->printNode($childNode);
        }

        return $output;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function printNode($node)
    {
        if ($node instanceof AlternativeNode) {
            return $this->printAlternativeNode($node);
        } elseif ($node instanceof BlockNode) {
            return $this->printBlockNode($node);
        } elseif ($node instanceof CharacterClassNode) {
            return $this->printCharacterClassNode($node);
        } elseif ($node instanceof RepetitionNode) {
            return $this->printRepetitionNode($node);
        } elseif ($node instanceof TokenNode) {
            return $this->printTokenNode($node);
        } elseif ($node instanceof BeginNode) {
            return $this->printBeginNode($node);
        } elseif ($node instanceof EndNode) {
            return $this->printEndNode($node);
        }
    }

    /**
     * @param AlternativeNode $node
     *
     * @return string
     */
    protected function printAlternativeNode(AlternativeNode $node)
    {
        $childNodes = $node->getChildNodes();

        return $this->printNode($childNodes[mt_rand(0, count($childNodes) - 1)]);
    }

    /**
     * @param BlockNode $node
     *
     * @return string
     */
    protected function printBlockNode(BlockNode $node)
    {
        $childNodes = $node->getChildNodes();

        if ($node->isSubPattern()) {
            $output = '';

            foreach ($childNodes as $childNode) {
                $output .= $this->printNode($childNode);
            }

            return $output;
        }

        return $this->printNode($childNodes[mt_rand(0, count($childNodes) - 1)]);
    }

    /**
     * @param BeginNode $node
     *
     * @return string
     */
    protected function printBeginNode(BeginNode $node)
    {
        $childNodes = $node->getChildNodes();
        $output = '';

        foreach ($childNodes as $childNode) {
            $output .= $this->printNode($childNode);
        }

        return $output;
    }

    /**
     * @param EndNode $node
     *
     * @return string
     */
    protected function printEndNode(EndNode $node)
    {
        $childNodes = $node->getChildNodes();
        $output = '';

        foreach ($childNodes as $childNode) {
            $output .= $this->printNode($childNode);
        }

        return $output;
    }

    /**
     * @param CharacterClassNode $node
     *
     * @return string
     */
    protected function printCharacterClassNode(CharacterClassNode $node)
    {
        $range = range($node->getStart()->getValue()->getValue(), $node->getEnd()->getValue()->getValue());

        return $range[mt_rand(0, count($range) - 1)];
    }

    /**
     * @param RepetitionNode $node
     *
     * @return string
     */
    protected function printRepetitionNode(RepetitionNode $node)
    {
        if ($node->getMax() !== null) {
            $count = mt_rand($node->getMin(), $node->getMax());
        } else {
            $count = mt_rand($node->getMin(), $node->getMin() + 5);
        }

        $output = '';

        for ($i = 0; $i < $count; ++$i) {
            foreach ($node->getChildNodes() as $childNode) {
                $output .= $this->printNode($childNode);
            }
        }

        return $output;
    }

    /**
     * @param TokenNode $node
     *
     * @return string
     */
    protected function printTokenNode(TokenNode $node)
    {
        $token = $node->getValue();

        if ($token instanceof EscapeToken) {
            // Not supported yet
            return '';
        }

        if ($token->is('T_PERIOD') &&
             (!($node->getParent() instanceof BlockNode) || ($node->getParent() instanceof BlockNode && $node->getParent()->isSubPattern()))) {
            $range = range('a', 'Z');

            return $range[mt_rand(0, count($range) - 1)];
        }

        return $token->getValue();
    }
}
