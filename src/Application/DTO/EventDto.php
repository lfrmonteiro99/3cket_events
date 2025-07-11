<?php

declare(strict_types=1);

namespace App\Application\DTO;

final class EventDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $location,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'event_name' => $this->name,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        if ($this->createdAt !== null) {
            $data['created_at'] = $this->createdAt;
        }

        if ($this->updatedAt !== null) {
            $data['updated_at'] = $this->updatedAt;
        }

        return $data;
    }
}
