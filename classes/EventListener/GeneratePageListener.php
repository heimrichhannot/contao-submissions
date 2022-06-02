<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Controller;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\FormModel;
use Contao\Input;
use Contao\PageRegular;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use HeimrichHannot\Submissions\Event\SubmissionsBeforeSendConfirmationNotificationEvent;
use HeimrichHannot\Submissions\SubmissionModel;
use HeimrichHannot\Submissions\Util\Tokens;
use NotificationCenter\tl_form;

/**
 * Hook("generatePage")
 */
class GeneratePageListener
{
    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        if (version_compare(VERSION, '4.7', '<')) {
            return;
        }

        $tokenId = Input::get('token');
        if (!$tokenId || FormGeneratorListener::OPTIN_TOKEN_PREFIX !== substr($tokenId, 0, 6)) {
            return;
        }

        $token = System::getContainer()->get('contao.opt-in')->find($tokenId);

        if (null === $token) {
            $submissions = SubmissionModel::findBy(["huhSubOptInTokenId=?"], [$tokenId]);
            if ($submissions) {
                while ($submissions->next()) {
                    $submissions->huhSubOptInTokenId = '';
                    $submissions->huhSubOptInCache = '';
                    $submissions->save();
                }
            }
            $this->errorRedirect('HuhSubInit01');
        }

        $submission = SubmissionModel::findBy(["huhSubOptInTokenId=?"], [$token->getIdentifier()]);
        if (!$submission || $submission->count() > 1) {
            $this->errorRedirect('HuhSubInit02');
        }
        $submission = $submission->current();

        $submissionCache = StringUtil::deserialize($submission->huhSubOptInCache, true);

        $form = FormModel::findByPk($submissionCache['form']);
        if (!$form) {
            $this->errorRedirect('HuhSubInit03');
        }

        if ($token->isConfirmed()) {
            $this->errorRedirect('Token already confirmed!', $form);
        }

        // Valid token, do confirm process

        $token->confirm();

        if ($form->huhSubOptInField) {
            $submission->{$form->huhSubOptInField} = "1";
        }

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
        $submission->huhSubOptInCache = serialize(['form' => $submissionCache['form'] ?? '']);
        $submission->save();


        /** @var PageModel|null $jumpTo */
        $jumpTo = $form->getRelated('huhSubOptInJumpTo');
        if (!$jumpTo instanceof PageModel) {
            $jumpTo = $GLOBALS['objPage'];
        }
        if ($jumpTo) {
            Controller::redirect($jumpTo->getFrontendUrl());
        } else {
            Controller::redirect("/");
        }
    }

    private function errorRedirect(string $errorCode, FormModel $form = null): void
    {
        if ($form && $form->huhSubOptInTokenInvalidJumpTo) {
            /** @var PageModel|null $jumpTo */
            $jumpTo = $form->getRelated('huhSubOptInTokenInvalidJumpTo');
            if ($jumpTo instanceof PageModel) {
                Controller::redirect($jumpTo->getFrontendUrl());
            }
        }

        Input::setGet('token', null);
        throw new PageNotFoundException('Invalid token!  (Error: '.$errorCode.')');
    }
}