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
                    ->defaultValue('file')
                    ->info('Service ID which is in charge for rates persistence.')
                ->end()
                ->append($this->getRatesDefinition())
                ->append($this->getFileRepositoryDefinition())
                ->append($this->getSourcesDefinition())
                ->append($this->getViewDefinition())
                ->append($this->getNotificationDefinition())
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
                        ->arrayNode('extra')->end()
                    ->end()
                ->end()
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
     * Build configuration tree for simple sources.
     *
     * @return ArrayNodeDefinition
     */
    protected function getSourcesDefinition()
    {
        $node = new ArrayNodeDefinition('sources');

        $node
            ->info('Add sources to sources registry without registering them into service container.')
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
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
                    ->defaultValue('@ExchangeRate/admin/base.html.twig')
                ->end()
                ->scalarNode('list')
                    ->info('Template for list view.')
                    ->defaultValue('@ExchangeRate/admin/list.html.twig')
                ->end()
                ->scalarNode('new')
                    ->info('Template for create new exchange rate view.')
                    ->defaultValue('@ExchangeRate/admin/new.html.twig')
                ->end()
                ->scalarNode('edit')
                    ->info('Template for edit exchange rate view.')
                    ->defaultValue('@ExchangeRate/admin/edit.html.twig')
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

    /**
     * Build configuration tree for e-mail notifications.
     *
     * @return ArrayNodeDefinition
     */
    public function getNotificationDefinition()
    {
        $node = new ArrayNodeDefinition('notifications');

        $node
            ->info('Notification settings.')
            ->children()
                ->arrayNode('fetch')
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Send e-mail report about fetch result and fetched rates.')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('to')
                            ->info('Recipients e-mail addresses.')
                        ->end()
                        ->arrayNode('to')
                            ->info('Blank carbon copy recipients e-mail addresses.')
                        ->end()
                        ->arrayNode('templates')
                            ->children()
                                ->scalarNode('success')
                                    ->isRequired()
                                    ->defaultValue('@ExchangeRate/mail/success.html.twig')
                                ->end()
                                ->scalarNode('error')
                                    ->isRequired()
                                    ->defaultValue('@ExchangeRate/mail/error.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }
}
