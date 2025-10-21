<?php

namespace HeimrichHannot\Submissions\EventListener\DataContainer\SubmissionArchive;

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Database;
use Contao\DataContainer;
use Contao\StringUtil;
use HeimrichHannot\Submissions\Security\SubmissionPermissions;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsCallback(table: 'tl_submission_archive', target: 'config.oncreate')]
class ConfigOnCreateListener
{
    public function __construct(
        private readonly RequestStack                  $requestStack,
        private readonly AuthorizationCheckerInterface $auth,
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
    }

    public function __invoke(string $table, int $id, array $row, DataContainer $dc): void
    {
        $this->setPermissions($id);
    }

    private function setPermissions(int $insertId): void
    {
        if ($this->auth->isGranted(SubmissionPermissions::USER_CAN_EDIT, $insertId)) {
            return;
        }

        /** @var BackendUser|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();
        if (null === $user) {
            return;
        }

        /** @var AttributeBagInterface $objSessionBag */
        $objSessionBag = $this->requestStack->getSession()->getBag('contao_backend');
        $newRecords = $objSessionBag->get('new_records');

        if (
            is_array($newRecords['tl_submission_archive'] ?? null) &&
            in_array($insertId, $newRecords['tl_submission_archive'])
        ) {
            $db = Database::getInstance();

            // Add the permissions on group level
            if ($user->inherit != 'custom')
            {
                $objGroup = $db->execute("SELECT id, submissionss, submissionsp FROM tl_user_group WHERE id IN("
                    . implode(',', array_map('\intval', $user->groups))
                    . ")");

                while ($objGroup->next())
                {
                    $listPermissions = StringUtil::deserialize($objGroup->submissionsp);

                    if (is_array($listPermissions) && in_array('create', $listPermissions))
                    {
                        $listSelects = array_map(
                            '\intval',
                            StringUtil::deserialize($objGroup->submissionss, true)
                        );
                        $listSelects[] = $insertId;

                        $db
                            ->prepare("UPDATE tl_user_group SET submissionss=? WHERE id=?")
                            ->execute(serialize($listSelects), $objGroup->id);
                    }
                }
            }

            // Add the permissions on user level
            if ($user->inherit != 'group')
            {
                $objUser = $db
                    ->prepare("SELECT submissionss, submissionsp FROM tl_user WHERE id=?")
                    ->limit(1)
                    ->execute($user->id);

                $listPermissions = StringUtil::deserialize($objUser->submissionsp);

                if (is_array($listPermissions) && in_array('create', $listPermissions))
                {
                    $listSelects = array_map(
                        '\intval',
                        StringUtil::deserialize($objUser->submissionss, true)
                    );
                    $listSelects[] = $insertId;

                    $db->prepare("UPDATE tl_user SET submissionss=? WHERE id=?")->execute(serialize($listSelects), $user->id);
                }
            }
        }
    }

}