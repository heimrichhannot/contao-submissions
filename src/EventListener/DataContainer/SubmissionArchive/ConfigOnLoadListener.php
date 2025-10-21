<?php

namespace HeimrichHannot\Submissions\EventListener\DataContainer\SubmissionArchive;

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsCallback(table: 'tl_submission_archive', target: 'config.onload')]
class ConfigOnLoadListener
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
    }

    public function __invoke(?DataContainer $dc = null): void
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof BackendUser || $user->isAdmin) {
            return;
        }

        // Set root IDs
        if (!$user->submissionss || !\is_array($user->submissionss)) {
            $root = [0];
        } else {
            $root = $user->submissionss;
        }

        $GLOBALS['TL_DCA']['tl_submission_archive']['list']['sorting']['root'] = $root;
    }
}