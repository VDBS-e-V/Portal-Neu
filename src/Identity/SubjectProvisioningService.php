<?php

declare(strict_types=1);

namespace App\Identity;

use App\Repository\IdentityAuthorizationRepository;

final class SubjectProvisioningService
{
    private IdentityAuthorizationRepository $identity;

    public function __construct(IdentityAuthorizationRepository $identity)
    {
        $this->identity = $identity;
    }

    public function ensureSubjectForPerson(int $personId): int
    {
        return $this->identity->ensureSubjectForPerson($personId);
    }
}
