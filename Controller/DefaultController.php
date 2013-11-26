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
}
