<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AdvancedUserPermissionsBundle\DataContainer;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Input;
use Symfony\Component\Security\Core\Security;

class CheckPermissionCallback
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
     * @Callback(table="tl_article", target="config.onload")
     */
    public function onLoadCallback(DataContainer $dc = null): void
    {
        if (!$dc || !\in_array($dc->table, ['tl_article', 'tl_news'])) {
            return;
        }

        $this->checkPermissions($dc->table);
    }

    private function checkPermissions(string $table): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        switch ($table) {
            case 'tl_article':
                $permission = 'contao_user.huhAdvUsPer_articlep';
                $type = 'articles';

                break;

            case 'tl_news':
                $permission = 'contao_user.huhAdvUsPer_newsArticlep';
                $type = 'news items';

                break;
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
            case 'move':
            case 'overrideAll':
            case 'cutAll':
                if (!$this->security->isGranted($permission, 'edit')) {
                    throw new AccessDeniedException('Not enough permissions to edit '.$type.'.');
                }

                break;

            case 'create':
            case 'copy':
            case 'copyAll':
                if (!$this->security->isGranted($permission, 'create')) {
                    throw new AccessDeniedException('Not enough permissions to create '.$type.'.');
                }

                break;

            case 'deleteAll':
            case 'delete':
                if (!$this->security->isGranted($permission, 'delete')) {
                    throw new AccessDeniedException('Not enough permissions to delete '.$type.'.');
                }

                break;
        }
    }
}
