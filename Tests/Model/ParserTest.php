<?php

namespace Kw\ParserBundle\Tests\Model;
use Kw\ParserBundle\Model\GLRParser;
use Kw\ParserBundle\Model\Lexer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * User: kay
 * Date: 21.11.13
 * Time: 17:13
 */

class ParserTest extends WebTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var GLRParser
     */
    private $parser;

    public function setUp()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->container = $container;

        $this->lexer = $this->container->get('kw_parser.lexer');

        $this->parser = $this->container->get('kw_parser.glrparser');
    }

    /**
     * provides array of string and expected result of parsing
     * @return array of string and token count
     */
    public function parseProvider()
    {
        return array(
            array('id', true),
            array('(id)', true),
            array('id*id', true),
            array('id+id', true),
            array('id+(id*id', false)
        );
    }

    /**
     * test tokenization of valid input strings by comparing the returned token count
     *
     * @dataProvider parseProvider
     */
    public function testParseResult($teststring, $expectedResult)
    {
        $tokens = $this->lexer->tokenize($teststring);
        $stack = array();
        $result = $this->parser->parse($tokens, $stack);

        // if true, the stack must contain 3 items (start state, root token, end state)
        if ($result) {
            $this->assertEquals(3, count($stack));
        }

        $this->assertEquals($expectedResult, $result);
    }
}