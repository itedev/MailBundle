<?php

namespace ITE\MailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ite_mail');

        $rootNode
            ->children()
                ->scalarNode('from_email')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('support_email')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('noreply_email')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('bcc_email')->defaultNull()->end()
                ->arrayNode('styles')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('style')->defaultValue('@ITEMailBundle/Resources/public/less/style.less')->end()
                    ->end()
                ->end()
                ->scalarNode('template_folder')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
