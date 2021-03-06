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

use RunOpenCode\Bundle\ExchangeRate\Enum\Role;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
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

        $rootNode = $treeBuilder->root('runopencode_exchange_rate');

        $rootNode
            ->fixXmlConfig('source', 'sources')
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
                ->append($this->getDoctrineDbalRepositoryDefinition())
                ->append($this->getSourcesDefinition())
                ->append($this->getAccessRolesDefinition())
                ->arrayNode('form_types')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getSourceTypeDefinition())
                        ->append($this->getRateTypeTypeDefinition())
                        ->append($this->getCurrencyCodeTypeDefinition())
                        ->append($this->getForeignCurrencyCodeTypeDefinition())
                        ->append($this->getRateTypeDefinition())
                    ->end()
                ->end()
                ->append($this->getNotificationsDefinition())
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
            ->beforeNormalization()
                ->ifTrue(function ($value) {
                    return array_key_exists('rate', $value);
                })
                ->then(function ($value) {
                    return $value['rate'];
                })
            ->end();

        $node
            ->fixXmlConfig('rate')
            ->info('Configuration of each individual rate with which you intend to work with.')
            ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('currency_code')->isRequired()->end()
                        ->scalarNode('rate_type')->isRequired()->end()
                        ->scalarNode('source')->isRequired()->end()
                        ->arrayNode('extra')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->variableNode('value')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Build configuration tree for file repository.
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
                    ->defaultValue('%kernel.root_dir%/../var/db/exchange_rates.dat')
                ->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for Doctrine Dbal repository.
     *
     * @return ArrayNodeDefinition
     */
    protected function getDoctrineDbalRepositoryDefinition()
    {
        $node = new ArrayNodeDefinition('doctrine_dbal_repository');

        $node
            ->info('Configuration for Doctrine Dbla repository (if used).')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('connection')
                    ->info('Which database connection to use.')
                    ->defaultValue('doctrine.dbal.default_connection')
                ->end()
                ->scalarNode('table_name')
                    ->info('Which table name to use for storing exchange rates.')
                    ->defaultValue('runopencode_exchange_rate')
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
        $node = new ArrayNodeDefinition('security');

        $node
            ->info('Configuration of security voter access roles.')
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->info('Enables or disables access controll.')
                    ->defaultTrue()
                ->end()
                ->arrayNode(AccessVoter::VIEW)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::VIEW_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(AccessVoter::CREATE)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::CREATE_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(AccessVoter::EDIT)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::EDIT_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(AccessVoter::DELETE)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::DELETE_RATE))
                    ->prototype('scalar')->end()
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
                ->scalarNode('choice_translation_domain')->defaultValue('runopencode_exchange_rate')->end()
                ->arrayNode('preferred_choices')->prototype('scalar')->end()->end()
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
                ->scalarNode('choice_translation_domain')->defaultValue('runopencode_exchange_rate')->end()
                ->arrayNode('preferred_choices')->prototype('scalar')->end()->end()
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
                ->scalarNode('choice_translation_domain')->defaultValue('runopencode_exchange_rate')->end()
                ->arrayNode('preferred_choices')->prototype('scalar')->end()->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for "RunOpenCode\Bundle\ExchangeRate\Form\Type\ForeignCurrencyCodeType" default settings.
     *
     * @return ArrayNodeDefinition
     */
    protected function getForeignCurrencyCodeTypeDefinition()
    {
        $node = new ArrayNodeDefinition('foreign_currency_code_type');

        $node
            ->info('Modify default "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\ForeignCurrencyCodeType" settings.')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('choice_translation_domain')->defaultValue('runopencode_exchange_rate')->end()
                ->arrayNode('preferred_choices')->prototype('scalar')->end()->end()
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
                ->scalarNode('choice_translation_domain')->defaultValue('runopencode_exchange_rate')->end()
                ->arrayNode('preferred_choices')->prototype('scalar')->end()->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Build configuration tree for notifications.
     *
     * @return ArrayNodeDefinition
     */
    protected function getNotificationsDefinition()
    {
        $node = new ArrayNodeDefinition('notifications');

        $node
            ->beforeNormalization()
                ->always(function ($value) {

                    if (isset($value['email'])) {
                        return ['e_mail' => $value['email']];
                    }

                    return $value;
                })
            ->end()
            ->info('Enable and configure notifications, or disable them.')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('e_mail')
                    ->fixXmlConfig('recipient', 'recipients')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Denotes if e-mail notifications are enabled.')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('recipients')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function ($value) {
                                    return [$value];
                                })
                            ->end()
                            ->info('E-mail addresses where notifications should be sent.')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();    

        return $node;
    }
}
