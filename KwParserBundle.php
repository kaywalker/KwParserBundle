<?php

namespace Kw\ParserBundle;

use Kw\ParserBundle\DependencyInjection\Compiler\LoadLRParserTablesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KwParserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LoadLRParserTablesCompilerPass());
    }
}
