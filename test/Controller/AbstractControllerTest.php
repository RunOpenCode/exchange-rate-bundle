<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Controller;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension as ExchangeRateBundleExtension;
use RunOpenCode\Bundle\ExchangeRate\ExchangeRateBundle;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\FormPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\Compiler as Twig;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class AbstractControllerTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Controller
 */
abstract class AbstractControllerTest extends TestCase
{

    protected function getContainer(array $configuration = [])
    {
        $configuration = array_merge([
            'grant_access' => false,
            'configuration' => [
                'runopencode_exchange_rate' => [
                    'base_currency' => 'RSD',
                    'rates' => [
                        ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
                        ['currency_code' => 'USD', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
                    ],
                    'sources' => [
                        'national_bank_of_serbia' => 'stdClass'
                    ],
                    'file_repository' => [
                        'path' => tempnam(sys_get_temp_dir(), 'runopencode_exchange_rate_bundle_repository_test')
                    ]
                ]
            ]
        ], $configuration);

        $container = new ContainerBuilder();

        $container->setParameter('kernel.root_dir', __DIR__.'/../Fixtures/app');
        $container->setParameter('kernel.cache_dir', __DIR__.'/../Fixtures/app/cache');
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.bundles_metadata', []);
        $container->setParameter('kernel.charset', 'UTF-8');
        $container->setParameter('kernel.environment', 'test');
        $container->setParameter('validator.translation_domain', 'en');

        $container
            ->addCompilerPass(new FormPass())
            ->addCompilerPass(new Twig\ExceptionListenerPass())
            ->addCompilerPass(new Twig\ExtensionPass())
            ->addCompilerPass(new Twig\RuntimeLoaderPass())
            ->addCompilerPass(new Twig\TwigEnvironmentPass())
            ->addCompilerPass(new Twig\TwigLoaderPass())
        ;

        $bundle = new ExchangeRateBundle();
        $bundle->build($container);

        $symfonyXmlLoader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../vendor/symfony/symfony/src/Symfony/Bundle/FrameworkBundle/Resources/config'));
        $symfonyXmlLoader->load('services.xml');
        $symfonyXmlLoader->load('form.xml');
        $symfonyXmlLoader->load('property_access.xml');
        $symfonyXmlLoader->load('translation.xml');
        $symfonyXmlLoader->load('validator.xml');

        $twigXmlLoader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../vendor/symfony/symfony/src/Symfony/Bundle/TwigBundle/Resources/config'));
        $twigXmlLoader->load('form.xml');
        $twigXmlLoader->load('templating.xml');
        $twigXmlLoader->load('twig.xml');

        $bundleXmlLoader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../src/RunOpenCode/Bundle/ExchangeRate/Resources/config/services'));
        $bundleXmlLoader->load('command.xml');
        $bundleXmlLoader->load('form_type.xml');
        $bundleXmlLoader->load('manager.xml');
        $bundleXmlLoader->load('processor.xml');
        $bundleXmlLoader->load('repository.xml');
        $bundleXmlLoader->load('security.xml');
        $bundleXmlLoader->load('source.xml');
        $bundleXmlLoader->load('validator.xml');


        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());

        $container->registerExtension(new FrameworkExtension());
        $container->registerExtension($twigExtension = new TwigExtension());
        $container->registerExtension($exchangeRateBundleExtension = new ExchangeRateBundleExtension());

        $exchangeRateBundleExtension->load([ 'runopencode_exchange_rate' => $configuration['configuration']['runopencode_exchange_rate'] ], $container);
        $twigExtension->load([
            'twig' => [
                'debug' => true,
                'cache' => false,
                'paths' => [
                    __DIR__.'/../../src/RunOpenCode/Bundle/ExchangeRate/Resources/views' => 'ExchangeRate'
                ]
            ]
        ], $container);

        $container->compile();

        $this
            ->mockSecurityAuthorizationChecker($container, $configuration);

        return $container;
    }

    private function mockSecurityAuthorizationChecker(ContainerBuilder $container, array $configuration)
    {

        $securityAuthorizationChecker = $this
            ->getMockBuilder(AuthorizationCheckerInterface::class)
            ->getMock();

        $securityAuthorizationChecker
            ->method('isGranted')
            ->willReturn($configuration['grant_access']);

        $container->set('security.authorization_checker', $securityAuthorizationChecker);

        return $this;
    }
}
