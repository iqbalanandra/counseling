<?php

namespace App\Application\DTOs;

class RoleDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $displayName,
        public readonly ?string $description = null,
        public readonly array $permissionIds = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            displayName: $data['display_name'],
            description: $data['description'] ?? null,
            permissionIds: $data['permission_ids'] ?? []
        );
    }
}
