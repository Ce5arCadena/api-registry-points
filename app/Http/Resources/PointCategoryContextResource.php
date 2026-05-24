<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointCategoryContextResource extends JsonResource
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
            "point_category_id"=> $this->point_category_id,
            "grade" => $this->course,
            "subject" => $this->subject,
            "status" => $this->status,  
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
