<?php

namespace HeimrichHannot\Submissions\DependencyInjection\Compiler;

use HeimrichHannot\FormTypeBundle\HeimrichHannotFormTypeBundle;
use HeimrichHannot\Submissions\FormType\SubmissionType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SubmissionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!\class_exists(HeimrichHannotFormTypeBundle::class)
            || !$container->has('huh.form_type')) {
            return;
        }

        $definition = (new Definition(SubmissionType::class))
            ->setAutoconfigured(true)
            ->setAutowired(true);

        $container->addDefinitions([$definition]);
    }
}
