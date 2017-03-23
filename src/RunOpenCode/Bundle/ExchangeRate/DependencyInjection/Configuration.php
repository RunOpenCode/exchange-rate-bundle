<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection;

use RunOpenCode\Bundle\ExchangeRate\Role;
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
                ->append($this->getAccessRolesDefinition())
                ->append($this->getNotificationDefinition())
                ->arrayNode('form_types')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getSourceTypeDefinition())
                        ->append($this->getRateTypeTypeDefinition())
                        ->append($this->getCurrencyCodeTypeDefinition())
                        ->append($this->getRateTypeDefinition())
                    ->end()
                ->end()
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
     * Build configuration tree for access roles.
     *
     * @return ArrayNodeDefinition
     */
    protected function getAccessRolesDefinition()
    {
        $node = new ArrayNodeDefinition('access_roles');

        $node
            ->info('Configuration of controller access roles.')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('list')
                    ->defaultValue(array(Role::MANAGE_RATE, Role::VIEW_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('create')
                    ->defaultValue(array(Role::MANAGE_RATE, Role::VIEW_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('edit')
                    ->defaultValue(array(Role::MANAGE_RATE, Role::DELETE_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('delete')
                    ->defaultValue(array(Role::MANAGE_RATE, Role::DELETE_RATE))
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ->end();


        return $node;
    }

    /**
     * Build configuration tree for notifications.
     *
     * @return ArrayNodeDefinition
     */
    protected function getNotificationDefinition()
    {
        $node = new ArrayNodeDefinition('notifications');

        $node
            ->info('Notification settings.')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('fetch')
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Send e-mail report about fetch result and fetched rates.')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('from')
                            ->info('Mail sender address.')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('to')
                            ->info('Recipients e-mail addresses.')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('cc')
                            ->info('Recipients e-mail addresses.')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('bcc')
                            ->info('Blank carbon copy recipients e-mail addresses.')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('templates')
                            ->addDefaultsIfNotSet()
                            ->info('Enable/disable individual mail notifications.')
                            ->children()
                                ->arrayNode('success')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->booleanNode('enabled')
                                            ->info('Enable/disable success notification.')
                                            ->defaultTrue()
                                        ->end()
                                        ->scalarNode('subject')
                                            ->info('Mail notification subject.')
                                            ->defaultValue('System notification: exchange rates successfully fetched.')
                                        ->end()
                                        ->scalarNode('template')
                                            ->info('Mail body template.')
                                            ->defaultValue('@ExchangeRate/mail/success.html.twig')
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('error')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->booleanNode('enabled')
                                            ->info('Enable/disable success notification.')
                                            ->defaultTrue()
                                        ->end()
                                        ->scalarNode('subject')
                                            ->info('Mail notification subject.')
                                            ->defaultValue('Error notification: exchange rates are not fetched.')
                                        ->end()
                                        ->scalarNode('template')
                                            ->info('Mail body template.')
                                            ->defaultValue('@ExchangeRate/mail/error.html.twig')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for "RunOpenCode\Bundle\ExchangeRate\Form\Type\SourceType" default settings.
     *
     * @return ArrayNodeDefinition
     */
    protected function getSourceTypeDefinition()
    {
        $node = new ArrayNodeDefinition('source_type');

        $node
            ->info('Modify default "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\SourceType" settings.')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('choice_translation_domain')->defaultValue('roc_exchange_rate')->end()
                ->arrayNode('preferred_choices')->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for "RunOpenCode\Bundle\ExchangeRate\Form\Type\RateTypeType" default settings.
     *
     * @return ArrayNodeDefinition
     */
    protected function getRateTypeTypeDefinition()
    {
        $node = new ArrayNodeDefinition('rate_type_type');

        $node
            ->info('Modify default "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\RateTypeType" settings.')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('choice_translation_domain')->defaultValue('roc_exchange_rate')->end()
                ->arrayNode('preferred_choices')->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for "RunOpenCode\Bundle\ExchangeRate\Form\Type\CurrencyCodeType" default settings.
     *
     * @return ArrayNodeDefinition
     */
    protected function getCurrencyCodeTypeDefinition()
    {
        $node = new ArrayNodeDefinition('currency_code_type');

        $node
            ->info('Modify default "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\CurrencyCodeType" settings.')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('choice_translation_domain')->defaultValue('roc_exchange_rate')->end()
                ->arrayNode('preferred_choices')->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for "RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType" default settings.
     *
     * @return ArrayNodeDefinition
     */
    protected function getRateTypeDefinition()
    {
        $node = new ArrayNodeDefinition('rate_type');

        $node
            ->info('Modify default "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\RateType" settings.')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('choice_translation_domain')->defaultValue('roc_exchange_rate')->end()
                ->scalarNode('label_format')->defaultValue('{{currency-code}}, {{rate-type}} ({{source}})')->end()
                ->arrayNode('preferred_choices')->end()
            ->end()
        ->end();

        return $node;
    }
}
