<?php

namespace Kw\ParserBundle\Model;

use Kw\ParserBundle\Model\Parser\Lexer;
use Kw\ParserBundle\Model\Parser\TokenFactory;


/**
 * Class SimpleLexer
 *
 * Tokenization is done by matching the regular expressions of the terminal symbols
 * to the beginning of the input string.
 *
 * @package Kw\ParserBundle\Model
 */
class SimpleLexer extends Lexer
{
    /**
     * named regular expressions. like:
     * array(
     *  'T_MYTERMINAL' => '[a-z]',
     *  'T_MYTERMINAL2' => '[A-Z],
     *  'myTerminal3' => '[0-9]
     * )
     */
    private $terminalSymbols;


    /**
     * @param $terminalSymbols the terminal symbols, as an associative array of regular expressions
     * @param TokenFactory $tokenFactory the tokenfactory to be used
     */
    public function __construct($terminalSymbols, TokenFactory $tokenFactory)
    {
        parent::__construct($tokenFactory);

        $this->terminalSymbols = $terminalSymbols;
    }


    /**
     * returns an array of tokens parsed from the given string.
     * the name of the token is the index of the regex in the terminals array
     * the value is set to the matching part of the regex.
     *
     * @param $string the string to be parsed
     * @return array of Tokens
     * @throws \Exception if the string cannot be parsed into tokens
     */
    public function tokenize($string)
    {
        $tokens = array();

        // repeat until $string is empty
        while ($string != '') {

            // no valid regex found yet
            $valid = false;

            // try to match each terminal from beginning of $string.
            foreach ($this->terminalSymbols as $name => $regex) {

                // if it matches, cut terminal from beginning of $string
                $value = $this->match($regex, $string);

                // did this regex match?
                if (!is_null($value)) {
                    $valid = true;

                    // create token, and add to result
                    $tokens[] = $this->tokenFactory->createToken($name, $value, $string);

                    // skip other terminals and restart with new $string
                    break;
                }

            }

            // no valid regex in all terminals?
            if (!$valid) {
                throw new \Exception(sprintf('illegal terminals in string: "%s"', $string));
            }

        }

        return $tokens;
    }


    /**
     * check if $terminal matches from the beginning of the string. if it does the matching part is cut from
     * the beginning of $string
     *
     * @param $regex the regex to match
     * @param &$string the string to be checked and reduced
     *
     * @return string if a match was found, null otherwise
     *
     * @throws \Exception if the regex matching of $terminal on $string has an error
     */
    private function match($regex, &$string)
    {
        // wrap the $regex to match from beginning and accept everything after it
        $pattern = '/^(' . $regex . ')([.\n]*)/';

        $result = preg_match($pattern, $string, $matches);

        // something wrong with regex expression?
        if ($result === false) {
            throw new \Exception('Could not apply regex on string');
        }

        // if 1 match found return the $regex matching part.
        if ($result == 1) {
            $match = $matches[1];
            $string = substr($string, strlen($match));
            return $match;
        }

        return null;
    }
}