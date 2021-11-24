<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Controller;
use Contao\FormModel;
use Contao\Input;
use Contao\PageModel;
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
            throw new \RuntimeException('Invalid token identifier');
        }

        if ($token->isConfirmed()) {
            throw new \RuntimeException('Token already confirmed');
        }

        $related = $token->getRelatedRecords();
        if (!isset($related['tl_form'])) {
            throw new \RuntimeException('Invalid token! (Error: HuhSubInit01)');
        }

        $form = FormModel::findByPk($related['tl_form'][0]);
        if (!$form) {
            throw new \RuntimeException('Invalid token! (Error: HuhSubInit02)');
        }

        $submission = SubmissionModel::findBy(["huhSubOptInTokenId=?"], [$token->getIdentifier()]);
        if (!$submission || $submission->count() > 1) {
            throw new \RuntimeException('Invalid token! (Error: HuhSubInit03)');
        }

        // Valid token, do confirm process

        $token->confirm();

        if ($form->huhSubOptInField) {
            $submission->{$form->huhSubOptInField} = "1";
        }

        /** @var tl_form $instance */
        $instance = System::importStatic(tl_form::class);
        if ($instance) {
            $submissionCache = deserialize($submission->huhSubOptInCache);
            $instance->sendFormNotification($submission->row(), $form->row(), $submissionCache['files'] ?? [], $submissionCache['labels'] ?? []);
        }

        // clean database
        $submission->huhSubOptInCache = '';
        $submission->huhSubOptInTokenId = '';
        $submission->save();

        /** @var PageModel|null $jumpTo */
        if (($jumpTo = $form->getRelated('huhSubOptInJumpTo')) instanceof PageModel)
        {
            Controller::redirect($jumpTo->getFrontendUrl());
        }
    }
}