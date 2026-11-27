<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name_en' => $this->name_en,
            'name_kh' => $this->name_kh,
            'is_active' => $this->is_active,
            'client' => $this->whenLoaded('client', fn() => $this->client ? [
                'id' => $this->client->id,
                'name_en' => $this->client->name_en,
                'name_kh' => $this->client->name_kh,
                'logo' => $this->client->logo,
                'base_currency' => $this->client->base_currency,
                'expired_at' => $this->client->expired_at,
            ] : null),
            'default_location' => $this->whenLoaded('defaultLocation', fn() => $this->defaultLocation ? [
                'id' => $this->defaultLocation->id,
                'name_en' => $this->defaultLocation->name_en,
                'name_kh' => $this->defaultLocation->name_kh,
            ] : null),
        ];
    }
}
