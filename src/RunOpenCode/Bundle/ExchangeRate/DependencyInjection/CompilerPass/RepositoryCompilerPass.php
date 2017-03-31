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
        $repository = $container->getParameter('run_open_code.exchange_rate.repository');

        if ($container->hasDefinition($repository)) {
            $container->setDefinition('run_open_code.exchange_rate.repository', $repository);
            return;
        }

        foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.repository') as $id => $tags) {

            foreach ($tags as $attributes) {
                
                if (isset($attributes['alias']) && $repository === $attributes['alias']) {
                    $container->setDefinition('run_open_code.exchange_rate.repository', $id);
                    return;
                }
            }
        }

        throw new ServiceNotFoundException(sprintf('Repository service "%s" does not exists.', $repository));
    }
}
