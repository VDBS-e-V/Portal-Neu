<?php

declare(strict_types=1);

namespace App\Identity;

final class IdentityPermissions
{
    public const SYSTEME_VIEW = 'identity.systeme.view';
    public const SYSTEME_CREATE = 'identity.systeme.create';
    public const SYSTEME_EDIT = 'identity.systeme.edit';
    public const SYSTEME_DELETE = 'identity.systeme.delete';

    public const PERMISSIONS_VIEW = 'identity.permissions.view';
    public const PERMISSIONS_CREATE = 'identity.permissions.create';
    public const PERMISSIONS_EDIT = 'identity.permissions.edit';
    public const PERMISSIONS_DELETE = 'identity.permissions.delete';

    public const GRUPPEN_VIEW = 'identity.gruppen.view';
    public const GRUPPEN_CREATE = 'identity.gruppen.create';
    public const GRUPPEN_EDIT = 'identity.gruppen.edit';
    public const GRUPPEN_DELETE = 'identity.gruppen.delete';
    public const GRUPPEN_PERMISSIONS_MANAGE = 'identity.gruppen.permissions.manage';

    public const SUBJECTS_VIEW = 'identity.subjects.view';
    public const SUBJECTS_GROUPS_VIEW = 'identity.subjects.groups.view';
    public const SUBJECTS_GROUPS_ASSIGN = 'identity.subjects.groups.assign';
    public const SUBJECTS_GROUPS_REMOVE = 'identity.subjects.groups.remove';
}
