<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        // Do something ?
    }
}
