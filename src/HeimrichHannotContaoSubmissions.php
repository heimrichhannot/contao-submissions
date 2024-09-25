<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Submissions;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoSubmissions extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}