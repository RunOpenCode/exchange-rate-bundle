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
        $knownRepositories = $this->getKnownRepositoryAliases();
        $repositoryServiceId = $container->getParameter('run_open_code.exchange_rate.repository');

        $repositoryServiceId = isset($knownRepositories[$repositoryServiceId]) ? $knownRepositories[$repositoryServiceId] : $repositoryServiceId;

        if (!$container->hasDefinition($repositoryServiceId)) {
            throw new \RuntimeException(sprintf('Unknown repository service "%s" referenced in configuration.', $repositoryServiceId));
        }

        $container->setDefinition('run_open_code.exchange_rate.repository', $container->getDefinition($repositoryServiceId));
    }

    /**
     * Get known repository aliases which can be used in configuration instead of full service name.
     *
     * @return array
     */
    protected function getKnownRepositoryAliases()
    {
        return array(
            'file' => 'run_open_code.exchange_rate.repository.file_repository'
        );
    }
}
