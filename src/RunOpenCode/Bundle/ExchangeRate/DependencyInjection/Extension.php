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

use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Configuration as TreeConfiguration;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Utils\CurrencyCodeUtil;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class Extension
 *
 * Bundle extension.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection
 */
class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return "runopencode_exchange_rate";
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://www.runopencode.com/xsd-schema/exchange-rate-bundle';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {

        $configuration = new TreeConfiguration();
        $config = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('repository.xml');
        $loader->load('command.xml');
        $loader->load('form_type.xml');
        $loader->load('manager.xml');
        $loader->load('processor.xml');
        $loader->load('security.xml');
        $loader->load('source.xml');
        $loader->load('validator.xml');
        $loader->load('notifications.xml');

        $this
            ->configureBaseCurrency($config, $container)
            ->configureRepository($config, $container)
            ->configureFileRepository($config, $container)
            ->configureDoctrineDbalRepository($config, $container)
            ->configureAccessVoter($config, $container)
            ->configureRates($config, $container)
            ->configureSources($config, $container)
            ->configureSourceType($config, $container)
            ->configureRateTypeType($config, $container)
            ->configureCurrencyCodeType($config, $container)
            ->configureForeignCurrencyCodeType($config, $container)
            ->configureRateType($config, $container)
            ->configureNotifications($config, $container)
        ;
    }

    /**
     * Configure base currency.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     * @throws \RunOpenCode\ExchangeRate\Exception\UnknownCurrencyCodeException
     */
    protected function configureBaseCurrency(array $config, ContainerBuilder $container)
    {
        $baseCurrency = CurrencyCodeUtil::clean($config['base_currency']);
        $container->setParameter('runopencode.exchange_rate.base_currency', $baseCurrency);

        return $this;
    }

    /**
     * Configure rates.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureRates(array $config, ContainerBuilder $container)
    {
        foreach ($config['rates'] as $rate) {
            $definition = new Definition(Configuration::class);

            $arguments = [
                $rate['currency_code'],
                $rate['rate_type'],
                $rate['source'],
                (isset($rate['extra']) && $rate['extra']) ? $rate['extra'] : []
            ];

            $definition
                ->setArguments($arguments)
                ->setPublic(false)
                ->addTag('runopencode.exchange_rate.rate_configuration')
                ;

            $container->setDefinition(sprintf('runopencode.exchange_rate.rate_configuration.%s.%s.%s', $rate['currency_code'], $rate['rate_type'], $rate['source']), $definition);
        }

        return $this;
    }


    /**
     * Configure sources which does not have to be explicitly added to service container.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureSources(array $config, ContainerBuilder $container)
    {
        if (!empty($config['sources'])) {

            foreach ($config['sources'] as $name => $class) {

                $definition = new Definition($class);
                $definition
                    ->addTag('runopencode.exchange_rate.source', ['name' => $name]);

                $container->setDefinition(sprintf('runopencode.exchange_rate.source.%s', $name), $definition);
            }
        }

        return $this;
    }

    /**
     * Configure required processors.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureRepository(array $config, ContainerBuilder $container)
    {
        $container->setParameter('runopencode.exchange_rate.repository', $config['repository']);
        return $this;
    }

    /**
     * Configure file repository.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureFileRepository(array $config, ContainerBuilder $container)
    {
        $defintion = $container->getDefinition('runopencode.exchange_rate.repository.file_repository');

        $defintion
            ->setArguments([
                $config['file_repository']['path'],
            ]);

        return $this;
    }

    /**
     * Configure Doctrine Dbal repository.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureDoctrineDbalRepository(array $config, ContainerBuilder $container)
    {
        $defintion = $container->getDefinition('runopencode.exchange_rate.repository.doctrine_dbal_repository');

        $defintion
            ->setArguments([
                new Reference($config['doctrine_dbal_repository']['connection']),
                $config['doctrine_dbal_repository']['table_name'],
            ]);

        return $this;
    }

    /**
     * Configure access voter.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureAccessVoter(array $config, ContainerBuilder $container)
    {
        if ($config['security']['enabled']) {

            $container
                ->getDefinition('runopencode.exchange_rate.security.access_voter')
                ->setArguments([
                    [
                        AccessVoter::VIEW => $config['security'][AccessVoter::VIEW],
                        AccessVoter::CREATE => $config['security'][AccessVoter::CREATE],
                        AccessVoter::EDIT => $config['security'][AccessVoter::EDIT],
                        AccessVoter::DELETE => $config['security'][AccessVoter::DELETE],
                    ]
                ]);

        } else {
            $container->removeDefinition('runopencode.exchange_rate.security.access_voter');
        }

        return $this;
    }

    /**
     * Configure "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\SourceType" default settings.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureSourceType(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('runopencode.exchange_rate.form_type.source_type');

        $arguments = $definition->getArguments();

        $arguments[1] = $config['form_types']['source_type'];

        $definition->setArguments($arguments);

        return $this;
    }

    /**
     * Configure "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\RateTypeType" default settings.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureRateTypeType(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('runopencode.exchange_rate.form_type.rate_type_type');

        $arguments = $definition->getArguments();

        $arguments[1] = $config['form_types']['rate_type_type'];

        $definition->setArguments($arguments);

        return $this;
    }

    /**
     * Configure "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\CurrencyCodeType" default settings.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureCurrencyCodeType(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('runopencode.exchange_rate.form_type.currency_code_type');

        $arguments = $definition->getArguments();

        $arguments[1] = CurrencyCodeUtil::clean($config['base_currency']);
        $arguments[2] = $config['form_types']['currency_code_type'];

        $definition->setArguments($arguments);

        return $this;
    }

    /**
     * Configure "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\ForeignCurrencyCodeType" default settings.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureForeignCurrencyCodeType(array $config, ContainerBuilder $container)
    {

        $definition = $container->getDefinition('runopencode.exchange_rate.form_type.foreign_currency_code_type');

        $arguments = $definition->getArguments();

        $arguments[1] = $config['form_types']['foreign_currency_code_type'];

        $definition->setArguments($arguments);

        return $this;
    }

    /**
     * Configure "RunOpenCode\\Bundle\\ExchangeRate\\Form\\Type\\RateType" default settings.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureRateType(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('runopencode.exchange_rate.form_type.rate_type');

        $arguments = $definition->getArguments();

        $arguments[1] = $config['form_types']['rate_type'];

        $definition->setArguments($arguments);

        return $this;
    }

    /**
     * Configure notifications
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     *
     * @return Extension $this Fluent interface.
     */
    protected function configureNotifications(array $config, ContainerBuilder $container)
    {
        if (isset($config['notifications']['e_mail']['enabled']) && $config['notifications']['e_mail']['enabled']) {
            $container->setParameter('runopencode.exchange_rate.notifications.e_mail', $config['notifications']['e_mail']['recipients']);
        }

        return $this;
    }
}
