<?php

$lang = &$GLOBALS['TL_LANG']['tl_form'];

$lang['huh_submissions_legend'] = 'Submissions settings';

$lang['huhSub_storeSubmission'] = ['Store as submission', "Save submitted form data as submission. This option overrides the \"store data\" option!"];
$lang['huhSub_submissionArchive'] = ['Submission archive', "The submissin archive which should store the submission data."];

$lang['huhSub_optIn']          = ['Activate double opt-in', "Activate double opt-in process for form submissions."];
$lang['huhSub_optInNotification'] = [
    'Double opt-in notification',
    "Choose an opt-in notification. This one will be sent before the default notification. The opt-in link is passed in the `optInUrl` token."
];
$lang['huhSub_optInJumpTo']       = ['Double opt-in success redirect page', "Choose a page to which the visitor will be redirected after successful opt-in."];
$lang['huhSub_optInTokenInvalidJumpTo'] = ['Token confirmed page', "Choose a page the visitor will be redirected to if the token is already confirmed."];
$lang['huhSub_optInField']        = ['Double opt-in confirmation field', "Choose a field that should set to true after a successful double opt-in."];

