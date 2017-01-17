<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(
    [
	'HeimrichHannot',]
);


/**
 * Register the classes
 */
ClassLoader::addClasses(
    [
	// Models
	'HeimrichHannot\Submissions\SubmissionModel'                  => 'system/modules/submissions/models/SubmissionModel.php',
	'HeimrichHannot\Submissions\SubmissionArchiveModel'           => 'system/modules/submissions/models/SubmissionArchiveModel.php',

	// Classes
	'HeimrichHannot\Submissions\Util\Tokens'                      => 'system/modules/submissions/classes/util/Tokens.php',
	'HeimrichHannot\Submissions\Submissions'                      => 'system/modules/submissions/classes/Submissions.php',
	'HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend' => 'system/modules/submissions/classes/backend/SubmissionArchiveBackend.php',
	'HeimrichHannot\Submissions\Backend\SubmissionBackend'        => 'system/modules/submissions/classes/backend/SubmissionBackend.php',]
);
