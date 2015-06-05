<?php

namespace ITE\MailBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TokenExtensionCompilerPass
 * @package ITE\MailBundle\DependencyInjection\Compiler
 */
class TokenExtensionCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite.mail.token_manager')) {
            return;
        }

        $definition = $container->getDefinition('ite.mail.token_manager');
        $taggedServices = $container->findTaggedServiceIds('ite.mail.extension');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addExtension', [
                new Reference($id)
            ]);
        }
    }

} 