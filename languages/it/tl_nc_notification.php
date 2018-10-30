<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_nc_notification'];

$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS] = 'Invio';
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION] =
	['Invio dei moduli', 'Questo tipo di notifica verrà utilizzato dopo aver inviato correttamente il modulo.'];
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_CONFIRMATION]
	= ['Conferma', 'Questo tipo di notifica viene utilizzato per confermare un invio e può anche essere inoltrato successivamente.'];
