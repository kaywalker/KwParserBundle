<?php

namespace Kw\ParserBundle\Model\Parser;

/**
 * Class Parser
 *
 * @package Kw\ParserBundle\Model\Parser
 */
abstract class Parser {

    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * Creates a Parser
     *
     * @param TokenFactory $tokenFactory
     */
    public function __construct(TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Parses the tokens
     *
     * @param $tokens array of Token
     * @param $stack array to be used as stack, if result is true the stack will contain 3 elements: start state, root token,
     * end state. on false it will contain the stack until illegal token was reached.
     * @return bool true on success, false otherwise
     */
    abstract public function parse($tokens, &$stack);
}