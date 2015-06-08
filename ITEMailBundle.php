<?php

namespace ITE\MailBundle;

use ITE\MailBundle\DependencyInjection\Compiler\TokenExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ITEMailBundle
 * @package ITE\MailBundle
 */
class ITEMailBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TokenExtensionCompilerPass());
    }
}
