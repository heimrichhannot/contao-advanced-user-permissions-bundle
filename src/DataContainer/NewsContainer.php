<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\DataContainer;

use Contao\Backend;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Security;

class NewsContainer
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
     * @Callback(table="tl_news", target="config.onload")
     */
    public function onLoadCallback(DataContainer $dc = null): void
    {
        $this->checkPermissions();
    }

    /**
     * @Callback(table="tl_news", target="list.operations.edit.button")
     * @Callback(table="tl_news", target="list.operations.editheader.button")
     * @Callback(table="tl_news", target="list.operations.cut.button")
     * @Callback(table="tl_news", target="list.operations.toggle.button")
     */
    public function onEditButtonCallback($row, $href, $label, $title, $icon, $attributes): string
    {
        return $this->createButton('edit', $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_news", target="list.operations.copy.button")
     */
    public function onCreateButtonCallback($row, $href, $label, $title, $icon, $attributes): string
    {
        return $this->createButton('create', $row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * @Callback(table="tl_news", target="list.operations.delete.button")
     */
    public function onCreateDeleteCallback($row, $href, $label, $title, $icon, $attributes): string
    {
        return $this->createButton('delete', $row, $href, $label, $title, $icon, $attributes);
    }

    private function createButton(string $right, $row, $href, $label, $title, $icon, $attributes): string
    {
        return $this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', $right) ? '<a href="'.Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    private function checkPermissions(): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        // Check current action
        switch (Input::get('act')) {
            case 'edit':
            case 'editAll':
            case 'cut':
            case 'toggle':
            case 'feature':
            case 'paste':
            case 'select':
            case 'overrideAll':
            case 'cutAll':
                if (!$this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'edit')) {
                    throw new AccessDeniedException('Not enough permissions to edit news.');
                }

                break;

            case 'create':
            case 'copyAll':
                if (!$this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'create')) {
                    throw new AccessDeniedException('Not enough permissions to create news items.');
                }

                break;

            case 'copy':
                if (!$this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'create')) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' news items.');
                }

                break;

            case 'deleteAll':
            case 'delete':
                if (!$this->security->isGranted('contao_user.huhAdvUsPer_newsArticlep', 'delete')) {
                    throw new AccessDeniedException('Not enough permissions to delete news items.');
                }

                break;
        }
    }
}
