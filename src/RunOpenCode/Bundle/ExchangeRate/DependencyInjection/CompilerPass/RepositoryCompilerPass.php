<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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

    protected function getKnownRepositoryAliases()
    {
        return array(
            'file' => 'run_open_code.exchange_rate.repository.file_repository'
        );
    }
}
