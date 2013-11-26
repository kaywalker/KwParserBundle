<?php

namespace Kw\ParserBundle\Model;

use Kw\ParserBundle\Model\Parser\TokenFactory;

class SimpleTokenFactory extends TokenFactory
{
    /**
     * @see TokenFactory
     */
    public function createToken($terminal, $value, $string)
    {
        return new SimpleToken($terminal, $value);
    }

}