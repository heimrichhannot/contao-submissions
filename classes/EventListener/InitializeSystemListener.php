<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Controller;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\FormModel;
use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\Submissions\SubmissionModel;
use NotificationCenter\tl_form;

/**
 * Hook("initializeSystem")
 */
class InitializeSystemListener
{
    public function __invoke(): void
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
            throw new PageNotFoundException('Invalid token!  (Error: HuhSubInit01)');
        }

        $submission = SubmissionModel::findBy(["huhSubOptInTokenId=?"], [$token->getIdentifier()]);
        if (!$submission || $submission->count() > 1) {
            throw new PageNotFoundException('Invalid token!  (Error: HuhSubInit02)');
        }

        $submissionCache = StringUtil::deserialize($submission->huhSubOptInCache, true);

        $form = FormModel::findByPk($submissionCache['form']);
        if (!$form) {
            throw new PageNotFoundException('Invalid token!  (Error: HuhSubInit03)');
        }

        if ($token->isConfirmed()) {
            if ($form->huhSubOptInTokenInvalidJumpTo) {
                /** @var PageModel|null $jumpTo */
                $jumpTo = $form->getRelated('huhSubOptInTokenInvalidJumpTo');
                if ($jumpTo instanceof PageModel) {
                    Controller::redirect($jumpTo->getFrontendUrl());
                }
                throw new PageNotFoundException('Invalid already confirmed!  (Error: HuhSubInit04)');
            }
            throw new \RuntimeException('Token already confirmed');
        }

        // Valid token, do confirm process

        $token->confirm();

        if ($form->huhSubOptInField) {
            $submission->{$form->huhSubOptInField} = "1";
        }

        /** @var tl_form $instance */
        $instance = System::importStatic(tl_form::class);
        if ($instance) {
            $submissionCache = StringUtil::deserialize($submission->huhSubOptInCache);
            $instance->sendFormNotification($submission->row(), $form->row(), $submissionCache['files'] ?? [], $submissionCache['labels'] ?? []);
        }

        // clean database
        $submission->huhSubOptInCache = '';
        $submission->huhSubOptInTokenId = '';
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

}
