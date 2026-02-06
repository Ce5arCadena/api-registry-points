<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "last_name" => $this->last_name,
            "document" => $this->document,
            "phone" => $this->phone,
            "grade" => new GradeResource($this->grade),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
