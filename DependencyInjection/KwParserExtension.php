<?php

namespace Kw\ParserBundle\DependencyInjection;

use Kw\ParserBundle\Model\LRParser;
use Kw\ParserBundle\Model\LRTablesGenerator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KwParserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $parser = new LRTablesGenerator(
            $container->getParameter('kw_parser.cfg.start'),
            $container->getParameter('kw_parser.cfg.productions'),
            $container->getParameter('kw_parser.cfg.terminals')
        );

        $container->setParameter('kw_parser.parser.lrtables', $parser->getTables());
        $container->setParameter('kw_parser.parser.productions', $parser->getProductions());
    }
}
