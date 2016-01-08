<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * Configuration tree.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection
 */
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
                ->scalarNode('base_currency')
                    ->isRequired()
                    ->info('Set base currency in which you are doing your business activities.')
                ->end()
                ->scalarNode('repository')
                    ->defaultValue('run_open_code.exchange_rate.repository.file_repository')
                    ->info('Service ID which is in charge for rates persistence.')
                ->end()
                ->append($this->getRatesDefinition())
                ->append($this->getProcessorsDefinition())
                ->append($this->getFileRepositoryDefinition())
                ->append($this->getViewDefinition())
            ->end()
        ->end();

        return $treeBuilder;
    }

    /**
     * Build configuration tree for rates.
     *
     * @return ArrayNodeDefinition
     */
    protected function getRatesDefinition()
    {
        $node = new ArrayNodeDefinition('rates');

        $node
            ->info('Configuration of each individual rate with which you intend to work with.')
            ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('currency_code')->isRequired()->end()
                        ->scalarNode('rate_type')->isRequired()->end()
                        ->scalarNode('source')->isRequired()->end()
                        ->scalarNode('alias')->defaultValue(null)->end()
                        ->arrayNode('extra')->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Build configuration tree for processors.
     *
     * @return ArrayNodeDefinition
     */
    protected function getProcessorsDefinition()
    {
        $node = new ArrayNodeDefinition('processors');

        $node
            ->info('List of processors which ought to be executed after fetch process.')
            ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();

        return $node;
    }

    /**
     * Build configuration tree for repository.
     *
     * @return ArrayNodeDefinition
     */
    protected function getFileRepositoryDefinition()
    {
        $node = new ArrayNodeDefinition('file_repository');

        $node
            ->info('Configuration for file repository (if used).')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('path')
                ->info('Absolute path to file where database file will be stored.')
                ->defaultValue('%kernel.root_dir%/db/exchange_rates.dat')
                ->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for view (controller).
     *
     * @return ArrayNodeDefinition
     */
    protected function getViewDefinition()
    {
        $node = new ArrayNodeDefinition('view');

        $node
            ->info('Configuration of administration interface.')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('base_template')
                    ->info('Base decorator template.')
                    ->defaultValue('@ExchangeRate/base.html.twig')
                ->end()
                ->scalarNode('list')
                    ->info('Template for list view.')
                    ->defaultValue('@ExchangeRate/list.html.twig')
                ->end()
                ->scalarNode('new')
                    ->info('Template for create new exchange rate view.')
                    ->defaultValue('@ExchangeRate/new.html.twig')
                ->end()
                ->scalarNode('edit')
                    ->info('Template for edit exchange rate view.')
                    ->defaultValue('@ExchangeRate/edit.html.twig')
                ->end()
                ->scalarNode('date_format')
                    ->info('Date format in list view.')
                    ->defaultValue('Y-m-d')
                ->end()
                ->scalarNode('time_format')
                    ->info('Date/time format in list view.')
                    ->defaultValue('H:i')
                ->end()
                ->booleanNode('secure')->defaultValue(true)->end()
            ->end()
        ->end();

        return $node;
    }
}
