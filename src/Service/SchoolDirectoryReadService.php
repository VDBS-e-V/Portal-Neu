<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\SchoolDirectoryRepository;
use App\Repository\SchoolOverrideRepository;

final class SchoolDirectoryReadService
{
    public function __construct(
        private readonly SchoolDirectoryRepository $schools,
        private readonly SchoolOverrideRepository $overrides
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function detail(int $schoolId): array
    {
        $school = $this->schools->find($schoolId);
        if ($school === []) {
            return [];
        }

        $site = $this->schools->primarySite($schoolId);
        $contacts = $this->schools->contacts($schoolId);
        $identifiers = $this->schools->identifiers($schoolId);
        $classifications = $this->schools->classifications($schoolId);
        $overrides = $this->overrides->findBySchoolIndexed($schoolId);

        return [
            'school' => $school,
            'site' => $site,
            'contacts' => $contacts,
            'identifiers' => $identifiers,
            'classifications' => $classifications,
            'overrides' => $overrides,
            'effective' => $this->effectiveValues($school, $site, $contacts, $overrides),
        ];
    }

    /**
     * @param array<string, mixed> $school
     * @param array<string, mixed> $site
     * @param array<int, array<string, mixed>> $contacts
     * @param array<string, array<string, mixed>> $overrides
     * @return array<string, mixed>
     */
    private function effectiveValues(array $school, array $site, array $contacts, array $overrides): array
    {
        return [
            'name' => $this->override($overrides, 'display_name', (string) ($school['name'] ?? '')),
            'email' => $this->override($overrides, 'primary_email', $this->primaryContact($contacts, 'email')),
            'phone' => $this->override($overrides, 'primary_phone', $this->primaryContact($contacts, 'phone')),
            'website' => $this->override($overrides, 'primary_website', $this->primaryContact($contacts, 'website')),
            'address_note' => $this->override($overrides, 'address_note', ''),
            'data_quality_note' => $this->override($overrides, 'data_quality_note', ''),
            'address_line' => trim(implode(' ', array_filter([
                (string) ($site['street'] ?? ''),
                (string) ($site['house_number'] ?? ''),
            ]))),
            'postal_city' => trim(implode(' ', array_filter([
                (string) ($site['postal_code'] ?? ''),
                (string) ($site['city'] ?? ''),
            ]))),
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $overrides
     */
    private function override(array $overrides, string $fieldKey, string $fallback): string
    {
        if (!array_key_exists($fieldKey, $overrides)) {
            return $fallback;
        }

        $value = $overrides[$fieldKey]['override_value'] ?? null;

        return $value === null ? $fallback : (string) $value;
    }

    /**
     * @param array<int, array<string, mixed>> $contacts
     */
    private function primaryContact(array $contacts, string $type): string
    {
        foreach ($contacts as $contact) {
            if ((string) ($contact['contact_type'] ?? '') === $type && (int) ($contact['is_primary'] ?? 0) === 1) {
                return (string) ($contact['value'] ?? '');
            }
        }

        foreach ($contacts as $contact) {
            if ((string) ($contact['contact_type'] ?? '') === $type) {
                return (string) ($contact['value'] ?? '');
            }
        }

        return '';
    }
}
