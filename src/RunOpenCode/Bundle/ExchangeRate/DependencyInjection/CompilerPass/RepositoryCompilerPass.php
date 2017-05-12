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
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class RepositoryCompilerPass
 *
 * Repository compiler pass
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class RepositoryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {

        if (!$container->hasParameter('runopencode.exchange_rate.repository')) {
            return;
        }

        $repository = $container->getParameter('runopencode.exchange_rate.repository');

        if ($container->has($repository)) {
            $container->setAlias('runopencode.exchange_rate.repository', $repository);
            $container->findDefinition('runopencode.exchange_rate.repository')->setPublic(true);
            return;
        }

        foreach ($container->findTaggedServiceIds('runopencode.exchange_rate.repository') as $id => $tags) {

            foreach ($tags as $attributes) {

                if (isset($attributes['alias']) && $repository === $attributes['alias']) {
                    $definition = $container->findDefinition($id);
                    $container->setDefinition('runopencode.exchange_rate.repository', $definition);
                    $definition->setPublic(true);
                    return;
                }
            }
        }

        throw new ServiceNotFoundException(sprintf('Repository service "%s" does not exists.', $repository));
    }
}
