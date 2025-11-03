<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Submissions;

use HeimrichHannot\Submissions\DependencyInjection\Compiler\SubmissionsPass;
use HeimrichHannot\Submissions\DependencyInjection\HeimrichHannotSubmissionsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotSubmissions extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtensionClass(): string
    {
        return HeimrichHannotSubmissionsExtension::class;
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        $this->extension ??= $this->createContainerExtension() ?: null;

        return $this->extension;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SubmissionsPass());
    }
}
