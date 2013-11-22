<?php

namespace Kw\ParserBundle\Tests\Model;
use Kw\ParserBundle\Model\Lexer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * User: kay
 * Date: 21.11.13
 * Time: 17:13
 */

class LexerTest extends WebTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Lexer
     */
    private $lexer;

    public function setUp()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->container = $container;

        /** @var Lexer $lexer */
        $this->lexer = $this->container->get('kw_parser.lexer');

    }

    /**
     * provides illegal input strings
     * @return array of illegal string
     */
    public function tokenizeIllegalStringProvider()
    {
        return array(
            array(' '),
            array('x'),
        );
    }

    /**
     * @dataProvider tokenizeIllegalStringProvider
     * @expectedException \Exception
     * @expectedExceptionMessage illegal terminals in string
     */
    public function testTokenizerIllegalString($teststring) {
        $this->lexer->tokenize($teststring);
    }

    /**
     * provides array of string and token count
     * @return array of string and token count
     */
    public function tokenizeProvider()
    {
        return array(
            array('id', 1),
            array('(id)', 3),
            array('id*id', 3),
            array('id+id', 3),
            array('id+(id*id)', 7)
        );
    }

    /**
     * test tokenization of valid input strings by comparing the returned token count
     *
     * @dataProvider tokenizeProvider
     */
    public function testTokenizeCount($teststring, $expectedResult)
    {
        $tokens =  $this->lexer->tokenize($teststring);

        $this->assertEquals($expectedResult, count($tokens));
    }
}