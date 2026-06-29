<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    /** @return iterable<object> */
    public function registerBundles(): iterable
    {
        /** @var array<class-string, array<string, bool>> $contents */
        $contents = require $this->getProjectDir() . '/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if (($envs[$this->environment] ?? false) || ($envs['all'] ?? false)) {
                yield new $class();
            }
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/config/packages/*.yaml', 'glob');
        $loader->load($this->getProjectDir() . '/config/services.yaml');
    }
}
