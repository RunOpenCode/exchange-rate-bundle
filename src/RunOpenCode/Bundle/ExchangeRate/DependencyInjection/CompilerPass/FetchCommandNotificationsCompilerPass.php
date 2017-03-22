<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use RunOpenCode\Bundle\ExchangeRate\Notification\MailNotification;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class FetchCommandNotificationsCompilerPass
 *
 * Fetch command notifications compiler pass
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class FetchCommandNotificationsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('run_open_code.exchange_rate.notifications.fetch') && $container->hasDefinition('run_open_code.exchange_rate.command.fetch')) {

            $configuration = $container->getParameter('run_open_code.exchange_rate.notifications.fetch');

            if (empty($configuration['to']) && empty($configuration['cc']) && empty($configuration['bcc'])) {
                throw new \RuntimeException('Mail notifications for fetch configuration are missing recipients. Did you forget to configure "to", "cc" and/or "bcc" field?');
            }

            $this
                ->processSuccessMailNotification($container, $configuration)
                ->processErrorMailNotification($container, $configuration)
            ;
        }
    }

    /**
     * Process success mail notifications.
     *
     * @param ContainerBuilder $container
     * @param array $configuration
     * @return FetchCommandNotificationsCompilerPass $this Fluent interface.
     */
    public function processSuccessMailNotification(ContainerBuilder $container, array $configuration)
    {
        if ($configuration['templates']['success']['enabled']) {

            $definition = new Definition(MailNotification::class);

            $definition
                ->setArguments(array(
                    new Reference('mailer'),
                    new Reference('templating'),
                    array(
                        'from' => $configuration['from'],
                        'to' => $configuration['to'],
                        'cc' => $configuration['cc'],
                        'bcc' => $configuration['bcc'],
                        'subject' => $configuration['templates']['success']['subject'],
                        'template' => $configuration['templates']['success']['template']
                    )
                ))
                ->setPublic(false)
                ;

            $container->setDefinition('run_open_code.exchange_rate.notifications.mail.fetch_command.success', $definition);

            $container
                ->getDefinition('run_open_code.exchange_rate.command.fetch')
                ->addMethodCall('addSuccessNotification', array(
                    new Reference('run_open_code.exchange_rate.notifications.mail.fetch_command.success')
                ));
        }

        return $this;
    }

    /**
     * Process error mail notifications.
     *
     * @param ContainerBuilder $container
     * @param array $configuration
     * @return FetchCommandNotificationsCompilerPass $this Fluent interface.
     */
    public function processErrorMailNotification(ContainerBuilder $container, array $configuration)
    {
        if ($configuration['templates']['error']['enabled']) {

            $definition = new Definition(MailNotification::class);

            $definition
                ->setArguments(array(
                    new Reference('mailer'),
                    new Reference('templating'),
                    array(
                        'from' => $configuration['from'],
                        'to' => $configuration['to'],
                        'cc' => $configuration['cc'],
                        'bcc' => $configuration['bcc'],
                        'subject' => $configuration['templates']['error']['subject'],
                        'template' => $configuration['templates']['error']['template']
                    )
                ))
                ->setPublic(false)
            ;

            $container->setDefinition('run_open_code.exchange_rate.notifications.mail.fetch_command.error', $definition);

            $container
                ->getDefinition('run_open_code.exchange_rate.command.fetch')
                ->addMethodCall('addErrorNotification', array(
                    new Reference('run_open_code.exchange_rate.notifications.mail.fetch_command.error')
                ));
        }

        return $this;
    }
}
