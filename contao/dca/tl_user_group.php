<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$dca = &$GLOBALS['TL_DCA']['tl_user_group'];

PaletteManipulator::create()
    ->addLegend('huhAdvUsPer_article_legend', 'filemounts_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('huhAdvUsPer_articlep', 'huhAdvUsPer_article_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group');

$dca['fields']['huhAdvUsPer_articlep'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'options' => ['create', 'edit', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['multiple' => true],
    'sql' => 'blob NULL',
];
