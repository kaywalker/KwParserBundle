<?php

namespace Kw\ParserBundle\Model\Parser;

/**
 * Class TokenFactory
 *
 * @package Kw\ParserBundle\Model\Parser
 */
abstract class TokenFactory
{

    /**
     * @param $terminal
     * @param $value
     * @param $string
     * @return Token
     */
    abstract public function createToken($terminal, $value, $string);

}