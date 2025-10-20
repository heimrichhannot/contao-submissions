<?php

namespace HeimrichHannot\Submissions\Security\DataContainer;

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Security\DataContainer\CreateAction;
use Contao\CoreBundle\Security\DataContainer\DeleteAction;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\CoreBundle\Security\DataContainer\UpdateAction;
use Contao\CoreBundle\Security\Voter\DataContainer\AbstractDataContainerVoter;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class SubmissionArchiveAccessVoter extends AbstractDataContainerVoter
{
    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager
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
            [ContaoCorePermissions::USER_CAN_ACCESS_MODULE],
            'huh_submissions')
        ) {
            return false;
        }

        return match (true) {
            $action instanceof CreateAction => $this->accessDecisionManager->decide($token, ['contao_user.submissionsp.create']),
            $action instanceof ReadAction,
                $action instanceof UpdateAction => $this->accessDecisionManager->decide($token, ['contao_user.submissionss'], $action->getCurrentId()),
            $action instanceof DeleteAction =>
                $this->accessDecisionManager->decide($token, ['contao_user.submissionss'], $action->getCurrentId()) &&
                $this->accessDecisionManager->decide($token, ['contao_user.submissionsp.delete']),
        };
    }
}