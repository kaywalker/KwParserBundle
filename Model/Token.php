<?php

namespace Kw\ParserBundle\Model;

class Token
{

    private $name = null;
    private $value = null;
    private $children = array();

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function addChild(Token $child)
    {
        array_unshift($this->children, $child);
    }

    public function __toString()
    {
        if (count($this->children) > 0) {
            $result = '';
            foreach ($this->children as $child) {
                $result .= (string) $child;
            }
            return $result;
        }
        return '<' . $this->getName() . ':"' . $this->getValue() . '">';
    }
}