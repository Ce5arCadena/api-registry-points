<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherSubjectCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'full_name'   => $this->full_name,
            'assignments' => ($this->subjectAssignments ?? collect())
                ->groupBy('grade_id')
                ->map(fn($items) => [
                    'grade'    => $items->first()->grade?->name,
                    'grade_id' => $items->first()->grade_id,
                    'subjects' => $items->map(fn($ts) => [
                        'id'   => $ts->subject?->id,
                        'name' => $ts->subject?->name,
                        'assignment_id' => $ts->id,
                        'year' => $ts->academic_year
                    ])->unique('id')->values()
                ])->values()
        ];
    }
}
