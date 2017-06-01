<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\NotificationsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class NotificationsCompilerPassTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection\CompilerPass
 */
class NotificationsCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function itRemovesEmailEventListenerWhenEmailNotificationsAreDisabled()
    {
        $this->setDefinition('runopencode.exchange_rate.notifications.email', new Definition());

        $this->compile();

        $this->assertFalse($this->container->has('runopencode.exchange_rate.notifications.email'));
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    public function itThrowsExceptionWhenMailerServiceIsMissing()
    {
        $this->setParameter('runopencode.exchange_rate.notifications.email.recipients', []);
        $this->setDefinition('runopencode.exchange_rate.notifications.email', new Definition());

        $this->compile();
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    public function itThrowsExceptionWhenThereAreNoEmailRecipients()
    {
        $this->setParameter('runopencode.exchange_rate.notifications.email.recipients', []);
        $this->setDefinition('runopencode.exchange_rate.notifications.email', new Definition());
        $this->setDefinition('mailer', new Definition());

        $this->compile();
    }

    /**
     * @test
     */
    public function itSetsRecipientsForEmailNotifications()
    {
        $this->setParameter('runopencode.exchange_rate.notifications.email.recipients', ['test@test.test']);
        $this->setDefinition('runopencode.exchange_rate.notifications.email', new Definition(null, [ 'arg1', 'arg2' ]));
        $this->setDefinition('mailer', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('runopencode.exchange_rate.notifications.email', 2, ['test@test.test']);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new NotificationsCompilerPass());
    }
}