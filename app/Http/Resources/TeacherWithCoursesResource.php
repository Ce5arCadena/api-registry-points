<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherWithCoursesResource extends JsonResource
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
            "full_name" => $this->full_name,
            "document" => $this->document,
            "phone" => $this->phone,
            "status" => $this->status,
            "grades" => GradeResourceWithSubjects::collection($this->grades),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
