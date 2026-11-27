<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return   [
            'id' => $this->id,
            'username' => $this->username,
            'name_en' => $this->name_en,
            'name_kh' => $this->name_kh,
            'is_active' => $this->is_active,
        ];
    }
}
