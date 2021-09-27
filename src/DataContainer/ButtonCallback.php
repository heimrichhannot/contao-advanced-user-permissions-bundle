<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\DataContainer;

use Contao\Backend;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\Security\Core\Security;

class ButtonCallback
{
    /** @var Security */
    private $security;

    /**
     * NewsContainer constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Callback(table="tl_news", target="list.operations.edit.button")
     * @Callback(table="tl_article", target="list.operations.edit.button")
     * @Callback(table="tl_news", target="list.operations.editheader.button")
     * @Callback(table="tl_news", target="list.operations.cut.button")
     */
    public function onEditButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ('tl_article' === $table) {
            if ($this->security->isGranted('contao_user.huhAdvUsPer_articlep', 'edit')) {
                $tlArticle = System::importStatic('tl_article');

                return $tlArticle->editArticle($row, $href, $label, $title, $icon, $attributes, $table);
            }
            $allowed = false;
        } else {
            $allowed = $this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'edit');
        }

        return $this->renderButton($allowed, $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_article", target="list.operations.editheader.button")
     */
    public function onEditHeaderButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ($this->security->isGranted('contao_user.huhAdvUsPer_articlep', 'edit')) {
            $tlArticle = System::importStatic('tl_article');

            return $tlArticle->editHeader($row, $href, $label, $title, $icon, $attributes, $table);
        }

        return $this->renderButton(false, $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_article", target="list.operations.copy.button")
     */
    public function onCopyButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ($GLOBALS['TL_DCA'][$table]['config']['closed']) {
            return '';
        }

        if ($this->security->isGranted('contao_user.huhAdvUsPer_articlep', 'create')) {
            $tlArticle = System::importStatic('tl_article');

            return $tlArticle->copyArticle($row, $href, $label, $title, $icon, $attributes, $table);
        }

        return $this->renderButton(false, $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_article", target="list.operations.cut.button")
     */
    public function onCutButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ($this->security->isGranted('contao_user.huhAdvUsPer_articlep', 'edit')) {
            $tlArticle = System::importStatic('tl_article');

            return $tlArticle->cutArticle($row, $href, $label, $title, $icon, $attributes, $table);
        }

        return $this->renderButton(false, $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_article", target="list.operations.delete.button")
     */
    public function onDeleteButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ($this->security->isGranted('contao_user.huhAdvUsPer_articlep', 'delete')) {
            $tlArticle = System::importStatic('tl_article');

            return $tlArticle->deleteArticle($row, $href, $label, $title, $icon, $attributes, $table);
        }

        return $this->renderButton(false, $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_article", target="list.operations.toggle.button")
     */
    public function onToggleButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ($this->security->isGranted('contao_user.huhAdvUsPer_articlep', 'edit')) {
            $tlArticle = System::importStatic('tl_article');

            return $tlArticle->toggleIcon($row, $href, $label, $title, $icon, $attributes, $table);
        }

        return $this->renderButton(false, $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_news", target="list.operations.copy.button")
     */
    public function onCreateButtonCallback($row, $href, $label, $title, $icon, $attributes): string
    {
        return $this->renderButton($this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'create'), $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_news", target="list.operations.delete.button")
     */
    public function onCreateDeleteCallback($row, $href, $label, $title, $icon, $attributes): string
    {
        return $this->renderButton($this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'delete'), $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_news", target="list.operations.toggle.button")
     */
    public function onNewsToggleButtonCallback($row, $href, $label, $title, $icon, $attributes, string $table): string
    {
        if ($this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'edit')) {
            $instance = System::importStatic('tl_news');

            return $instance->toggleIcon($row, $href, $label, $title, $icon, $attributes, $table);
        }

        return $this->renderButton(false, $row, $href, $label, $title, $icon, $attributes);
    }

    private function renderButton(bool $allowed, $row, $href, $label, $title, $icon, $attributes): string
    {
        return $allowed ? '<a href="'.Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }
}
