<?php

namespace Kw\ParserBundle\Model\Parser;

/**
 * Class Lexer
 *
 * @package Kw\ParserBundle\Model\Parser
 */
abstract class Lexer
{
    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * Create a TokenFactory
     *
     * @param TokenFactory $tokenFactory
     */
    public function __construct(TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }


    /**
     * returns an array of tokens parsed from the given string
     *
     * @param $string string the string to be parsed
     *
     * @return array of Token
     *
     * @throws \Exception if the string cannot be parse into tokens
     */
    abstract public function tokenize($string);

}