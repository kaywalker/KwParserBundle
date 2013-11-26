<?php

namespace Kw\ParserBundle\Controller;

use Kw\ParserBundle\Model\Lexer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render(
            'KwParserBundle:Default:index.html.twig',

            array(
                'tables' =>  $this->container->getParameter('kw_parser.parser.lrtables'),
                'productions' => $this->container->getParameter('kw_parser.parser.productions'),
            )
        );
    }

    public function editorAction()
    {
        return $this->render(
            'KwParserBundle:Default:editor.html.twig'
        );
    }

    public function saveAction()
    {
        $input = $this->getRequest()->get('input');

        $tokens = $this->container->get('kw_parser.lexer')->tokenize($input);

        $stack = array();
        $result = $this->container->get('kw_parser.parser')->parse($tokens, $stack);
        var_dump($stack);

        return $this->render(
            'KwParserBundle:Default:editor.html.twig'
        );
    }

}
