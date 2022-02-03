<?php

$lang = &$GLOBALS['TL_LANG']['tl_form'];

$lang['storeAsSubmission'] = ['Store as submission', "Save submitted form data as submission. This option overrides the \"store data\" option!"];
$lang['submissionArchive'] = ['Submission archive', "The submissin archive which should store the submission data."];

$lang['huhSubAddOptIn']          = ['Activate double opt-in', "Activate double opt-in process for form submissions."];
$lang['huhSubOptInNotification'] = [
    'Double opt-in notification',
    "Choose an opt-in notification. This one will be sent before the default notification. The opt-in link is passed in the `optInUrl` token."
];
$lang['huhSubOptInJumpTo']       = ['Double opt-in success redirect page', "Choose a page to which the visitor will be redirected after successful opt-in."];
$lang['huhSubOptInTokenInvalidJumpTo'] = ['Token confirmed page', "Choose a page the visitor will be redirected to if the token is already confirmed."];
$lang['huhSubOptInField']        = ['Double opt-in confirmation field', "Choose a field that should set to true after a successful double opt-in."];

