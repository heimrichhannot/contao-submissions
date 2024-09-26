<?php

/**
 * @package HeimrichHannot Contao Submissions Bundle
 * @copyright Heimrich & Hannot GmbH, 2024
 * @license https://spdx.org/licenses/LGPL-3.0-or-later.html LGPL-3.0-or-later
 */

namespace HeimrichHannot\Submissions\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use HeimrichHannot\CleanerBundle\HeimrichHannotContaoCleanerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Plugin implements BundlePluginInterface, ConfigPluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HeimrichHannotContaoCleanerBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
    {
        $loader->load('@HeimrichHannotSubmissions/config/services.yaml');
    }
}