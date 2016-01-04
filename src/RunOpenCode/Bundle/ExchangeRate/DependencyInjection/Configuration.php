<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('run_open_code_exchange_rate');

        $rootNode
            ->children()
                ->scalarNode('base_currency')->isRequired()->end()
                ->scalarNode('repository')->defaultValue('run_open_code.exchange_rate.repository.file_repository')->end()
                ->append($this->getRatesDefinition())
                ->append($this->getProcessorsDefinition())
                ->append($this->getFileRepositoryDefinition())
                ->append($this->getViewDefinition())
            ->end()
        ->end();

        return $treeBuilder;
    }

    protected function getRatesDefinition()
    {
        $node = new ArrayNodeDefinition('rates');

        $node
            ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('currency_code')->isRequired()->end()
                        ->scalarNode('rate_type')->isRequired()->end()
                        ->scalarNode('source')->isRequired()->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    protected function getProcessorsDefinition()
    {
        $node = new ArrayNodeDefinition('processors');

        $node
            ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();

        return $node;
    }

    protected function getFileRepositoryDefinition()
    {
        $node = new ArrayNodeDefinition('file_repository');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('path')->defaultValue('%kernel.root_dir%/db/exchange_rates.dat')->end()
            ->end()
        ->end();

        return $node;
    }

    protected function getViewDefinition()
    {
        $node = new ArrayNodeDefinition('view');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('base_template')->defaultValue('@ExchangeRate/base.html.twig')->end()
                ->scalarNode('list')->defaultValue('@ExchangeRate/list.html.twig')->end()
                ->scalarNode('new')->defaultValue('@ExchangeRate/new.html.twig')->end()
                ->scalarNode('edit')->defaultValue('@ExchangeRate/edit.html.twig')->end()
                ->scalarNode('date_format')->defaultValue('Y-m-d')->end()
                ->scalarNode('date_time_format')->defaultValue('Y-m-d H:i')->end()
            ->end()
        ->end();

        return $node;
    }
}
