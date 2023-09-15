<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\EventListener\Callback;

use Contao\Backend;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\Security\Core\Security;

class NewsCallbackListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Callback(table="tl_news", target="list.operations.toggle.button")
     */
    public function onListOperationsToggleButton(array $row,
                                                 ?string $href,
                                                 string $label,
                                                 string $title,
                                                 ?string $icon,
                                                 string $attributes,
                                                 string $table,
                                                 array $rootRecordIds,
                                                 ?array $childRecordIds,
                                                 bool $circularReference,
                                                 ?string $previous,
                                                 ?string $next,
                                                 DataContainer $dc): string
    {
        if (!$this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'edit')) {
            return '';
        }

        if (!method_exists($dc, 'toggle')) {
            $instance = System::importStatic('tl_news');

            return \call_user_func_array([$instance, 'toggleIcon'], \func_get_args());
        }

        return $this->toggleButton('tl_news', 'published', $row, $href, $label, $title, $icon);
    }

    /**
     * @Callback(table="tl_news", target="list.operations.feature.button")
     */
    public function onListOperationsFeaturedButtonCallback(
        array $row, ?string $href, string $label, string $title, ?string $icon, string $attributes, string $table, array $rootRecordIds, ?array $childRecordIds, bool $circularReference, ?string $previous, ?string $next, DataContainer $dc
    ): string {
        if (!$this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'edit')) {
            return '';
        }

        if (!method_exists($dc, 'toggle')) {
            $instance = System::importStatic('tl_news');

            return \call_user_func_array([$instance, 'iconFeatured'], \func_get_args());
        }

        return $this->toggleButton('tl_news', 'featured', $row, $href, $label, $title, $icon);
    }

    private function toggleButton(string $table, string $field, array $row, ?string $href, string $label, string $title, ?string $icon): string
    {
        // Hide the toggle icon if the user does not have access to the field
        if (($GLOBALS['TL_DCA'][$table]['fields'][$field]['toggle'] ?? false) !== true || (($GLOBALS['TL_DCA'][$table]['fields'][$field]['exclude'] ?? false) && !$this->security->isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE, $table.'::'.$field))) {
            return '';
        }

        $href = Backend::addToUrl(($href ?? '').'&amp;id='.$row['id'].(Input::get('nb') ? '&amp;nc=1' : ''));

        $_icon = pathinfo($icon, \PATHINFO_FILENAME).'_.'.pathinfo($icon, \PATHINFO_EXTENSION);

        if (false !== strpos($icon, '/')) {
            $_icon = \dirname($icon).'/'.$_icon;
        }

        if ('visible.svg' == $icon) {
            $_icon = 'invisible.svg';
        }

        $state = $row[$field] ? 1 : 0;

        if ($v['reverse'] ?? false) {
            $state = $row[$field] ? 0 : 1;
        }

        return '<a href="'.$href.'" title="'.StringUtil::specialchars($title).'" onclick="Backend.getScrollOffset();return AjaxRequest.toggleField(this,'.('visible.svg' == $icon ? 'true' : 'false').')">'
            .Image::getHtml($state ? $icon : $_icon, $label, 'data-icon="'.Image::getPath($icon).'" data-icon-disabled="'.Image::getPath($_icon).'" data-state="'.$state.'"')
            .'</a> ';
    }
}
