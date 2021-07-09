<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;

class NewsContainer
{
    /**
     * @Callback(table="tl_news", target="config.onload")
     */
    public function onLoadCallback(DataContainer $dc = null): void
    {
    }
}
