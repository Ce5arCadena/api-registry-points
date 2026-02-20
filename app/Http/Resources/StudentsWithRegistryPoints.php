<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentsWithRegistryPoints extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "lastName" => $this->last_name,
            "document" => $this->document,
            "phone" => $this->phone,
            "registryPoints" => $this->registryPoints
        ];
    }
}