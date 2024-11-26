<?php

namespace HeimrichHannot\Submissions\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\OptIn\OptIn;
use Contao\FormModel;
use Contao\Input;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\StringUtil;
use Contao\System;
use Exception;
use HeimrichHannot\Submissions\Config\OptInConfig;
use HeimrichHannot\Submissions\Event\SubmissionsBeforeSendConfirmationNotificationEvent;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\Submissions\Util\Tokens;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

##[AsHook("getPageLayout")]
readonly class GetPageLayoutListener
{
    public function __construct(
        private OptIn $optIn
    ) {}

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        $tokenId = Input::get('token');
        if (!$tokenId || !\is_string($tokenId) || !\str_starts_with($tokenId, OptInConfig::TOKEN_PREFIX)) {
            return;
        }

        [$token, $submission, $submissionCache, $form] = $this->processToken($tokenId);

        // Valid token, do confirm process

        $token->confirm();

        if ($form->huhSub_optInField) {
            $submission->{$form->huhSub_optInField} = "1";
        }

        /*
        todo: re-enable event when adding notifications
        */

        $submissionData = $submission->row();

        if (!empty($submissionCache['files']) && is_array($submissionCache['files'])) {
            $submissionData = Tokens::addAttachmentTokens($submissionData, $submissionCache['files']);
        }

        $event = new SubmissionsBeforeSendConfirmationNotificationEvent($submission, $submissionCache, $form, $submissionData);
        try {
            System::getContainer()->get('event_dispatcher')->dispatch(
                $event,
                SubmissionsBeforeSendConfirmationNotificationEvent::class
            );
        } catch (\Exception $e) {
            if (System::getContainer()->get('kernel')->isDebug()) {
                throw $e;
            } else {
                System::log(
                    "Exception while executing SubmissionsBeforeSendConfirmationNotificationEvent",
                    __METHOD__,
                    TL_ERROR
                );
            }
        }

        /** @var tl_form $instance */
        $instance = System::importStatic(tl_form::class);
        if ($instance) {
            $submissionCache = StringUtil::deserialize($submission->huhSubOptInCache);
            $instance->sendFormNotification(
                Tokens::cleanInvalidTokens($event->getSubmissionData()),
                Tokens::cleanInvalidTokens($form->row()),
                $submissionCache['files'] ?? [],
                $submissionCache['labels'] ?? []
            );
        }

        // clean database
        $submission->huhSub_optInCache = \serialize(['form' => $submissionCache['form'] ?? '']);
        $submission->save();

        /** @var PageModel|null $jumpTo */
        $jumpTo = $form->getRelated('huhSub_optInJumpTo');

        if (!$jumpTo instanceof PageModel)
        {
            $jumpTo = null;

            try {
                $jumpTo = System::getContainer()->get('contao.routing.page_finder')?->getCurrentPage();
            } catch (ServiceNotFoundException) {}

            $jumpTo ??= $GLOBALS['objPage'] ?? null;
        }

        Controller::redirect($jumpTo ? $jumpTo->getFrontendUrl() : "/");
    }

    protected function processToken(string $tokenId): array
    {
        if (!$token = $this->optIn->find($tokenId))
        {
            if ($submissions = SubmissionModel::findBy(["huhSub_optInTokenId=?"], [$tokenId]))
            {
                while ($submissions->next())
                {
                    $submissions->huhSub_optInTokenId = '';
                    $submissions->huhSub_optInCache = '';
                    $submissions->save();
                }
            }

            $this->abort('Invalid Token (huh:submissions:generatePage:01)');
        }

        $submission = SubmissionModel::findBy(["huhSub_optInTokenId=?"], [$token->getIdentifier()]);
        if (!$submission || $submission->count() > 1) {
            $this->abort('Internal Server Error (huh:submissions:generatePage:02)');
        }
        $submission = $submission->current();

        $submissionCache = StringUtil::deserialize($submission->huhSub_optInCache, true);
        if (!$form = FormModel::findByPk($submissionCache['form'])) {
            $this->abort('Internal Server error (huh:submissions:generatePage:03)');
        }

        if ($token->isConfirmed()) {
            $this->abort('Token already confirmed!', $form);
        }

        if (!$token->isValid()) {
            $this->abort('Token is expired or no longer valid.', $form);
        }

        return [$token, $submission, $submissionCache, $form];
    }

    /**
     * @param string $errorCode
     * @param FormModel|null $form
     * @return never
     * @throws Exception
     */
    protected function abort(string $errorCode, FormModel $form = null): never
    {
        if ($form && $form->huhSub_optInTokenInvalidJumpTo)
        {
            /** @var PageModel|null $jumpTo */
            $jumpTo = $form->getRelated('huhSub_optInTokenInvalidJumpTo');
            if ($jumpTo instanceof PageModel) {
                Controller::redirect($jumpTo->getFrontendUrl());
            }
        }

        Input::setGet('token', null);
        throw new PageNotFoundException(
            ($GLOBALS['TL_LANG']['ERR']['submission']['invalidToken'] ?? 'Invalid token!').'  (Error: '.$errorCode.')'
        );
    }
}