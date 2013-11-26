<?php

namespace Kw\ParserBundle\DependencyInjection\Compiler;

use Kw\ParserBundle\Model\LRTablesGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LoadLRParserTablesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $cfgs = $container->getParameter('kw_parser.config');

        $terminals = $cfgs['terminals'];
        $start = $cfgs['start'];
        $productions = $cfgs['productions'];

        $parser = new LRTablesGenerator($start, $productions, $terminals);

        $container->setParameter('kw_parser.cfg.terminals', $terminals);
        $container->setParameter('kw_parser.parser.lrtables', $parser->getTables());
        $container->setParameter('kw_parser.parser.productions', $parser->getProductions());
    }
}