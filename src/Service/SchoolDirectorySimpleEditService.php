<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\SchoolDirectoryRepository;
use App\Repository\SchoolOverrideRepository;
use InvalidArgumentException;

final class SchoolDirectorySimpleEditService
{
    private const ALLOWED_LIFECYCLE_STATUS = ['active', 'inactive', 'unknown', 'merged', 'closed'];
    private const ALLOWED_DATA_STATUS = ['imported', 'manually_created', 'manually_verified', 'needs_review'];

    /** @var array<int, string> */
    private const OVERRIDE_FIELDS = [
        'display_name',
        'primary_email',
        'primary_phone',
        'primary_website',
        'address_note',
        'data_quality_note',
    ];

    public function __construct(
        private readonly SchoolDirectoryRepository $schools,
        private readonly SchoolOverrideRepository $overrides
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $schoolId, array $data, ?int $personId): void
    {
        $lifecycleStatus = trim((string) ($data['lifecycle_status'] ?? ''));
        $dataStatus = trim((string) ($data['data_status'] ?? ''));

        if (!in_array($lifecycleStatus, self::ALLOWED_LIFECYCLE_STATUS, true)) {
            throw new InvalidArgumentException('Ungültiger Lebenszyklusstatus.');
        }

        if (!in_array($dataStatus, self::ALLOWED_DATA_STATUS, true)) {
            throw new InvalidArgumentException('Ungültiger Datenstatus.');
        }

        $reason = trim((string) ($data['reason'] ?? ''));
        $reason = $reason === '' ? null : $reason;

        foreach (self::OVERRIDE_FIELDS as $fieldKey) {
            $value = trim((string) ($data[$fieldKey] ?? ''));
            if ($value === '') {
                $this->overrides->deleteByField($schoolId, $fieldKey);
                continue;
            }

            $this->validateField($fieldKey, $value);
            $this->overrides->upsert($schoolId, $fieldKey, $value, $reason, $personId);
        }

        $this->schools->updateStatus($schoolId, $lifecycleStatus, $dataStatus);
    }

    private function validateField(string $fieldKey, string $value): void
    {
        if ($fieldKey === 'primary_email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Die E-Mail-Korrektur ist keine gültige E-Mail-Adresse.');
        }

        if ($fieldKey === 'primary_website' && !filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Die Website-Korrektur ist keine gültige URL.');
        }
    }
}
