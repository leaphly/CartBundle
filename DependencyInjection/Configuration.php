<?php

namespace Leaphly\CartBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    static $supportedDrivers = array('orm', 'mongodb');
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('leaphly_cart');

        $rootNode
            ->children()
                ->scalarNode('cart_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('cart_manager')->defaultValue('leaphly_cart.cart_manager.default')->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
                ->scalarNode('product_family_provider')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('use_price_listener')->defaultTrue()->end()
                ->scalarNode('cart_transition')->defaultValue('leaphly_cart.cart.finite.transition')->end()
            ->end();

        $this->addDbDriver($rootNode);
        $this->addRoles($rootNode);

        return $treeBuilder;
    }

    private function addDbDriver(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray(self::$supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode(self::$supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    private function addRoles(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('roles')
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->children()
                    ->booleanNode('is_default')->defaultFalse()->end()
                    ->arrayNode('handler')
                    ->canBeUnset()
                        ->children()
                            ->scalarNode('cart')->cannotBeEmpty()->end()
                            ->scalarNode('item')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('controller')
                        ->children()
                            ->scalarNode('cart')->cannotBeEmpty()->end()
                            ->scalarNode('item')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->scalarNode('form')->end()
                    ->scalarNode('strategy')->end()
                    ->scalarNode('fallback_strategy')->end()
                ->end()
                ->validate()
                    ->ifTrue(function ($v) {return !isset($v['form']) && !isset($v['handler']['cart']);})
                    ->thenInvalid('You need to specify or the form or the cart handler.')
                ->end()
            ->end()
            ->isRequired()
            ->cannotBeEmpty()
            ->validate()
                ->ifTrue(function ($v) {return (count($v) < 1);})
                ->thenInvalid('You need to specify at least one role.')
            ->end()
            ->validate()
                ->ifTrue(function ($roles) {
                    $counter = 0;
                    foreach ($roles as $role) {
                        $counter += (isset($role['is_default']) && $role['is_default'])? 1 : 0;
                    }

                    return ($counter > 1);
                })
                ->thenInvalid('Multiple `is_default` defined.')
            ->end()
        ->end()
        ;
    }
}
