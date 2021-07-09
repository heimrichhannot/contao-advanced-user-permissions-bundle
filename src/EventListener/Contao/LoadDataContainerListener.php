<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\EventListener\Contao;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_user_group':
                $this->addNewsPermission($table);

                break;
        }
    }

    private function addNewsPermission(string $table): void
    {
        if (!class_exists("Contao\NewsBundle\ContaoNewsBundle")) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA'][$table];

        PaletteManipulator::create()
            ->addField('huhAdvUsPer_newsArticlep', 'newp', PaletteManipulator::POSITION_AFTER)
            ->applyToPalette('default', $table);

        $dca['fields']['huhAdvUsPer_newsArticlep'] = [
            'exclude' => true,
            'inputType' => 'checkbox',
            'options' => ['create', 'edit', 'delete'],
            'default' => [
                'create' => true, 'edit' => true, 'delete' => true,
            ],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['multiple' => true],
            'sql' => 'blob NULL',
        ];
    }
}
