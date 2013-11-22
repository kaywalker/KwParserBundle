<?php

namespace Kw\ParserBundle\Model;

/**
 * User: kay
 * Date: 21.11.13
 * Time: 19:38
 */

class GLRParser {

    private $tables;

    public function __construct($lrtables, $productions)
    {
        $this->tables = $lrtables;
        $this->productions = $productions;
    }

    /**
     * Parses the tokens
     *
     * @param array of Token
     * @return bool
     */
    public function parse($tokens)
    {
        $state = 0;

        $stack = array();
        $stack[] = $state;

        while ((count($tokens) > 0 || count($stack) > 3)) {

            $token = array_shift($tokens);
            if (is_null($token)) {
                $token = new Token('$', '');
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
                    $newToken = new Token($productionName, $productionName);


                    for ($index=0; $index<count($production[1]); $index++) {
                        // remove state
                        array_pop($stack);

                        // remove token
                        $oldtoken = array_pop($stack);


                        $newToken->addChild($oldtoken);

                    }
                    $state = array_pop($stack);
                    array_push($stack, $state);


                    array_push($stack, $newToken);


                    $newState = $this->getGoto($state, $newToken->getName());
                    $state = $newState;
                    array_push($stack, $newState);
                }
            } else {
                return false;
            }
        }



        return true;
    }

    public function getActions($state, $terminal)
    {
        $index = array_search($terminal, $this->tables[1][0]);
        if ($index === false) {

        } else {
            $actionsTable = $this->tables[1][1];
            if (array_key_exists($state, $actionsTable)) {
                if (array_key_exists($index, $actionsTable[$state])) {
                    return $actionsTable[$state][$index];
                }
            }
        }

        return null;
    }


    public function getGoto($state, $terminal)
    {
        $index = array_search($terminal, $this->tables[0][0]);
        if ($index === false) {

        } else {

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