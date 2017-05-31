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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class NotificationsCompilerPass
 *
 * Compiler pass for notifications
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class NotificationsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('runopencode.exchange_rate.notifications.email')) {
            return;
        }

        if (!$container->hasParameter('runopencode.exchange_rate.notifications.e_mail')) {
            $container->removeDefinition('runopencode.exchange_rate.notifications.email');
            return;
        }

        if (!$container->hasDefinition('mailer') || !class_exists('\\Symfony\\Bundle\\SwiftmailerBundle\\SwiftmailerBundle')) {
            throw new RuntimeException('Bundle "symfony/swiftmailer-bundle" is required for email notifications.');
        }

        $recipients = $container->getParameter('runopencode.exchange_rate.notifications.e_mail');

        if (0 === count($recipients)) {
            throw new RuntimeException('At least one recipient for e-mail notifications must be provided.');
        }

        $container
            ->getDefinition('runopencode.exchange_rate.notifications.e_mail')
            ->addArgument($recipients);
    }
}
