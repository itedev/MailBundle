<?php

namespace ITE\MailBundle;

use ITE\MailBundle\DependencyInjection\Compiler\TokenExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MailBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TokenExtensionCompilerPass());
    }
}
