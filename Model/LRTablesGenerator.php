<?php

namespace Kw\ParserBundle\Model;

class LRTablesGenerator
{
    private $terminals;

    private $productions;

    private $tables;


    public function __construct($start, $productions, $terminals)
    {
        $this->terminals = $terminals;

        $this->productions = array();
        foreach ($productions as $name => $rules) {
            foreach ($rules as $rule) {
                $this->productions[] = array( $name, $rule);
            }
        }
        // insert start production
        array_unshift( $this->productions, array("start", array($start)));
        foreach ($this->productions as $num => $production) {
            @$this->production_sets[serialize($production)] &= $this->production_sets[$num] = $this->item_set($production);
            @$this->production_index[$production[0]][] = $num;
        }
        $this->start_symbol = $start;
        $this->tables = $this->build();
    }



    public function getTables()
    {
        return $this->tables;
    }

    public function getActionIndex()
    {
        return $this->tables[1][0];
    }

    public function getActionTable()
    {
        return $this->tables[1][1];
    }

    public function getGotoIndex()
    {
        return $this->tables[0][0];
    }
    public function getGotoTable()
    {
        return $this->tables[0][1];
    }

    public function getProductions()
    {
        return $this->productions;
    }

    public function getTerminals()
    {
        return $this->terminals;
    }

    public function buildGraph()
    {
        $graphViz = new \Image_GraphViz();

        $terminals = $this->getTerminals();

        foreach ($this->getActionTable() as $state => $actions) {

            $sourceState = $state;

            foreach ($terminals as $terminalName => $regex) {
                $action = $this->getActions($state, $terminalName);

                if ($action[0] == 'acc') {
                    $graphViz->addEdge ( array (
                        $sourceState.'' => 'end'
                    ), array (
                        'label' => 'acc'
                    ) );
                }
                if (is_array($action) && array_key_exists(0, $action)) {

                    $style = 'solid';
                    $label = $terminalName;
                    if (substr($action[0],0,1) == 's') {

                        $targetState = substr($action[0],1);
                        $label = 'S: ' . $label;
                        $graphViz->addEdge ( array (
                            $sourceState.'' => $targetState.''
                        ), array (
                            'label' => $label,
                            'style' => $style,
                        ) );

                    }
                }

            }
            foreach ($this->getGotoIndex() as $key => $symbolName) {
                $goto = $this->getGoto($state, $symbolName);
                var_dump($state . ', ' . $goto);
                if ($goto) {
                    $style = 'dashed';

                    $targetState = substr($action[0],1);
                    $graphViz->addEdge ( array (
                        $sourceState.'' => $goto.''
                    ), array (
                        'label' => 'J: ' . $symbolName,
                        'style' => $style,
                    ) );
                }
            }
        }
        error_reporting(0);
        return $graphViz->fetch ();
    }















    private function build()
    {
        $transition_index = array();
        $first = $this->close_set(array($this->production_sets[0][0]));
        $next_round = array($first);
        $reserved = 2;
        while (!empty($next_round)) {
            $new_round = array();
            foreach ($next_round as $item_set) {
                $this->item_sets[] = $item_set;
                $reserved--;
                $transition_sets = $this->generate_transition_sets($item_set);
                foreach ($transition_sets as $leading_symbol => $transition_set) {
                    $serialized_transition_set = serialize($transition_set);
                    if (isset($transition_index[$serialized_transition_set])) {
                        $this->transition_table[count($this->item_sets) - 1][$leading_symbol] = $transition_index[$serialized_transition_set];
                    } else {
                        $transition_index[$serialized_transition_set] = count($this->item_sets) - 1 + $reserved;
                        $this->transition_table[count($this->item_sets) - 1][$leading_symbol] = count($this->item_sets) - 1 + $reserved;
                        $new_round[] = $transition_set;
                        $reserved++;
                    }
                }
            }
            $next_round = $new_round;
        }
        return $this->create_action_and_goto();
    }

    private function create_action_and_goto()
    {
        $action_index = $goto_index = array();
        foreach ($this->item_sets as $num => $item_set) {
            $goto[$num] = array();
        }
        $action = $goto;

        foreach ($this->transition_table as $item_set => $transition) {
            foreach ($transition as $leading_symbol => $set) {
                if (strtolower($leading_symbol) == $leading_symbol) {
                    if (!in_array($leading_symbol, $goto_index)) {
                        $goto_index[] = $leading_symbol;
                        $pos = count($goto_index) - 1;
                    } else {
                        $pos = array_search($leading_symbol, $goto_index);
                    }
                    $goto[$item_set][$pos] = $set;
                } else {
                    if (!in_array($leading_symbol, $action_index)) {
                        $action_index[] = $leading_symbol;
                        $pos = count($action_index) - 1;
                    } else {
                        $pos = array_search($leading_symbol, $action_index);
                    }
                    $action[$item_set][$pos][] = "s$set";
                }
            }
        }

        $action_index[] = "$";
        foreach ($this->item_sets as $state => $item_set) {
            foreach ($item_set as $item) {
                if ($item == array("start", array(array($this->start_symbol), "*", array()))) {
                    $action[$state][count($action_index) - 1][] = "acc";
                } elseif (empty($item[1][2])) {
                    $production = array($item[0], array_merge($item[1][0], $item[1][2]));
                    $reduce = array_search($production, $this->productions);
                    $i = 0;
                    for ($x = count($action_index); $x > 0; $x--) {
                        $action[$state][$i][] = "r$reduce";
                        $i++;
                    }
                }
            }
        }

        return array(array($goto_index, $goto), array($action_index, $action));
    }

    private function generate_transition_sets($item_set)
    {
        $leading_symbols = array();
        foreach ($item_set as $item) {
            if (!empty($item[1][2])) {
                $shift = $this->shift_item_set(array($item));
                $leading_symbols[$item[1][2][0]][] = $shift[0];
            }
        }
        foreach ($leading_symbols as $lead => $item_set) {
            $leading_symbols[$lead] = $this->close_set($item_set);
        }
        return $leading_symbols;
    }

    private function shift_item_set($item_set)
    {
        foreach ($item_set as $lol => $item) {
            array_push($item_set[$lol][1][0], array_shift($item_set[$lol][1][2]));
        }
        return $item_set;
    }

    private function close_set($item_set)
    {
        $new_set = $item_set;
        $nonterminals = array();
        do {
            $item_set = $new_set;
            foreach ($item_set as $item) {
                $nonterminal = $item[1][2];
                if (isset($nonterminal[0]) && (strtolower($nonterminal[0]) == $nonterminal[0]) && !in_array($nonterminal[0], $nonterminals)) {
                    foreach ($this->production_index[$nonterminal[0]] as $production_id) {
                        if (!in_array($this->production_sets[$production_id][0], $item_set))
                            $new_set = array_merge($new_set, array($this->production_sets[$production_id][0]));
                    }
                    $nonterminals[] = $item[1][2][0];
                }
            }
        } while ($item_set != $new_set);
        return $this->unique_items($new_set);
    }

    private function unique_items($item_set)
    {
        foreach ($item_set as $num => $set) {
            $item_set[$num] = serialize($set);
        }
        $item_set = array_keys(array_flip($item_set));
        foreach ($item_set as $num => $set) {
            $item_set[$num] = unserialize($set);
        }
        return $item_set;
    }

    private function item_set($production)
    {
        $item_set = array();
        $left = array();
        $right = $production[1];
        while (true) {
            $item_set[] = array($production[0], array($left, "*", $right));
            if (empty($right)) break;
            array_push($left, array_shift($right));
        }
        return $item_set;
    }


}