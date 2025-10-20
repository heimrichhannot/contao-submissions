<?php

namespace HeimrichHannot\Submissions\EventListener\DataContainer\SubmissionArchive;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;
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
        if ($this->auth->isGranted(GuidedTourPermissions::USER_CAN_EDIT, $insertId)) {
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
            is_array($newRecords['tl_guided_tour_archive'] ?? null) &&
            in_array($insertId, $newRecords['tl_guided_tour_archive'])
        ) {
            $db = Database::getInstance();

            // Add the permissions on group level
            if ($user->inherit != 'custom')
            {
                $objGroup = $db->execute("SELECT id, guided_tours, guided_tourp FROM tl_user_group WHERE id IN("
                    . implode(',', array_map('\intval', $user->groups))
                    . ")");

                while ($objGroup->next())
                {
                    $listPermissions = StringUtil::deserialize($objGroup->guided_tourp);

                    if (is_array($listPermissions) && in_array('create', $listPermissions))
                    {
                        $listSelects = array_map(
                            '\intval',
                            StringUtil::deserialize($objGroup->guided_tours, true)
                        );
                        $listSelects[] = $insertId;

                        $db
                            ->prepare("UPDATE tl_user_group SET guided_tours=? WHERE id=?")
                            ->execute(serialize($listSelects), $objGroup->id);
                    }
                }
            }

            // Add the permissions on user level
            if ($user->inherit != 'group')
            {
                $objUser = $db
                    ->prepare("SELECT guided_tours, guided_tourp FROM tl_user WHERE id=?")
                    ->limit(1)
                    ->execute($user->id);

                $listPermissions = StringUtil::deserialize($objUser->guided_tourp);

                if (is_array($listPermissions) && in_array('create', $listPermissions))
                {
                    $listSelects = array_map(
                        '\intval',
                        StringUtil::deserialize($objUser->guided_tours, true)
                    );
                    $listSelects[] = $insertId;

                    $db->prepare("UPDATE tl_user SET guided_tours=? WHERE id=?")->execute(serialize($listSelects), $user->id);
                }
            }
        }
    }

}