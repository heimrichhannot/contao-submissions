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
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'HeimrichHannot\Submissions\Submissions'            => 'system/modules/submissions/classes/Submissions.php',
	'HeimrichHannot\Submissions\SubmissionsCleaner'     => 'system/modules/submissions/classes/SubmissionsCleaner.php',

	// Models
	'HeimrichHannot\Submissions\SubmissionModel'        => 'system/modules/submissions/models/SubmissionModel.php',
	'HeimrichHannot\Submissions\SubmissionArchiveModel' => 'system/modules/submissions/models/SubmissionArchiveModel.php',
));
