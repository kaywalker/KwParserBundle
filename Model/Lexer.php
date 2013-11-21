<?php

namespace Kw\ParserBundle\Model;

class Lexer
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

    public function __construct($terminalSymbols)
    {
        $this->terminalSymbols = $terminalSymbols;
    }


    /**
     * returns an array of tokens parsed from the given string
     *
     * @param $string the string to be parsed
     *
     * @return array of Tokens
     *
     * @throws \Exception if the string cannot be parse into tokens
     */
    public function tokenize($string)
    {
        $tokens = array();

        // repeat until $string is empty
        while ($string != '') {

            // try to match each terminal from beginning of $string.
            foreach ($this->terminalSymbols as $name => $regex) {

                // if it matches cut terminal from $string
                $value = $this->match($regex, $string);
                if (!is_null($value)) {
                    $tokens[] = new Token($name, $value);

                    // skip other terminals and restart with new $string
                    break;
                }

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
    public function match($regex, &$string)
    {
        $pattern = '/^(' . $regex . ')([.\n]*)/';

        $result = preg_match($pattern, $string, $matches);

        if ($result === false) {
            throw new \Exception('An error ocurrend');
        }

        if ($result == 1) {
            $match = $matches[1];
            $string = substr($string, strlen($match));
            return $match;
        }

        return null;
    }
}