<?php

declare(strict_types=1);

namespace App\Identity;

final class BiblioCollectPermissions
{
    public const MEDIEN_VIEW = 'bibliocollect.medien.view';
    public const MEDIEN_CREATE = 'bibliocollect.medien.create';
    public const MEDIEN_EDIT = 'bibliocollect.medien.edit';
    public const MEDIEN_DELETE = 'bibliocollect.medien.delete';

    public const AUSLEIHE_CREATE = 'bibliocollect.ausleihe.create';
    public const AUSLEIHE_RETURN = 'bibliocollect.ausleihe.return';
}
