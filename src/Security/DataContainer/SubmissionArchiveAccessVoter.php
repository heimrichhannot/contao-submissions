<?php

namespace HeimrichHannot\Submissions\Security\DataContainer;

use Contao\CoreBundle\Security\DataContainer\CreateAction;
use Contao\CoreBundle\Security\DataContainer\DeleteAction;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\CoreBundle\Security\DataContainer\UpdateAction;
use Contao\CoreBundle\Security\Voter\DataContainer\AbstractDataContainerVoter;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Security\SubmissionPermissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class SubmissionArchiveAccessVoter extends AbstractDataContainerVoter
{
    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function getTable(): string
    {
        return SubmissionArchiveModel::getTable();
    }

    protected function hasAccess(TokenInterface $token, UpdateAction|CreateAction|ReadAction|DeleteAction $action): bool
    {
        if (!$this->accessDecisionManager->decide(
            $token,
            [SubmissionPermissions::USER_CAN_ACCESS_MODULE]
        )) {
            return false;
        }

        return match (true) {
            $action instanceof CreateAction => $this->accessDecisionManager->decide(
                $token,
                [SubmissionPermissions::USER_CAN_CREATE]
            ),
            $action instanceof ReadAction,
            $action instanceof UpdateAction => $this->accessDecisionManager->decide(
                $token,
                [SubmissionPermissions::USER_CAN_EDIT],
                $action->getCurrentId()
            ),
            $action instanceof DeleteAction => $this->accessDecisionManager->decide($token, [SubmissionPermissions::USER_CAN_EDIT], $action->getCurrentId())
                && $this->accessDecisionManager->decide($token, [SubmissionPermissions::USER_CAN_DELETE]),
        };
    }
}
