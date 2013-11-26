<?php

namespace Kw\ParserBundle\Model;

use Kw\ParserBundle\Model\Parser\Parser;
use Kw\ParserBundle\Model\Parser\TokenFactory;

class GLRParser extends Parser {

    private $tables;

    /**
     * Create a new GLRParser
     *
     * @param $lrtables the action and goto lookup tables
     * @param $productions the production rules
     */
    public function __construct($lrtables, $productions, TokenFactory $tokenFactory)
    {
        parent::__construct($tokenFactory);

        $this->tables = $lrtables;
        $this->productions = $productions;
    }

    /**
     * Parses the tokens
     *
     * @param $tokens array of Token
     * @param $stack array to be used as stack, if result is true the stack will contain 3 elements: start state, root token,
     * end state. on false it will contain the stack until parsing stopped.
     * @return bool true on success, false otherwise
     */
    public function parse($tokens, &$stack)
    {
        $state = 0;

        $stack[] = $state;

        while ((count($tokens) > 0 || count($stack) > 3)) {

            $token = array_shift($tokens);
            if (is_null($token)) {
                $token = $this->tokenFactory->createToken('$', '', '');
            }

            $actions = $this->getActions($state, $token->getName());

            if ($actions) {

                $actions = $this->getActions($state, $token->getName());
                $action = $actions[0];

                if ($action == 'acc') {
                    //echo $stack[1];
                    //var_dump($stack[1]);
                    return true;
                }
                if ($action[0] == 's') {
                    $state = substr($action, 1);
                    array_push($stack, $token);
                    array_push($stack, $state);

                } else if ($action[0] == 'r') {

                    array_unshift($tokens, $token);

                    $production = $this->productions[substr($action, 1)];
                    $productionName = $production[0];
                    $newToken = $this->tokenFactory->createToken($productionName, $productionName, '');

                    for ($index=0; $index<count($production[1]); $index++) {
                        // remove state
                        array_pop($stack);

                        // remove token
                        $oldtoken = array_pop($stack);

                        // add to children of new token
                        $newToken->addChild($oldtoken);
                    }
                    // pop and push to set current state without changing stack
                    $state = array_pop($stack);
                    array_push($stack, $state);

                    // look up and set current state to new state
                    $newState = $this->getGoto($state, $newToken->getName());
                    $state = $newState;

                    // push new token and new state
                    array_push($stack, $newToken);
                    array_push($stack, $newState);
                }
            } else {
                return false;
            }
        }



        return true;
    }


    /**
     * returns action array for given state and terminal symbol
     *
     * @param $state the state
     * @param $terminal the terminal
     * @return null|array
     */
    public function getActions($state, $terminal)
    {
        $index = array_search($terminal, $this->tables[1][0]);
        if ($index !== false) {
            $actionsTable = $this->tables[1][1];
            if (array_key_exists($state, $actionsTable)) {
                if (array_key_exists($index, $actionsTable[$state])) {
                    return $actionsTable[$state][$index];
                }
            }
        }

        return null;
    }


    /**
     * return goto state for given state and nonterminal symbol
     *
     * @param $state the state
     * @param $nonterminal the nonterminal
     * @return null|array
     */
    public function getGoto($state, $nonterminal)
    {
        $index = array_search($nonterminal, $this->tables[0][0]);
        if ($index !== false) {
            $gotoTable = $this->tables[0][1];
            if (array_key_exists($state, $gotoTable)) {
                if (array_key_exists($index, $gotoTable[$state])) {
                    return $gotoTable[$state][$index];
                }
            }
        }

        return null;
    }
}