<?php

namespace App\Http\Controllers;

use App\Models\TeacherSubject;
use App\Http\Services\TeacherSubjectService;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateTeacherSubjectRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeacherSubjectController extends Controller
{
    public function __construct(protected TeacherSubjectService $teacherSubjectService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherSubjectRequest $request)
    {
        try {
            return $this->teacherSubjectService->asignSubjectToTeacher($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al asignar la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherSubject $teacherSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherSubject $teacherSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherSubjectRequest $request, int $teacherSubject)
    {
        try {
            return $this->teacherSubjectService->updateAsignSubjectToTeacher($request, $teacherSubject);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la asignación de la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherSubject $teacherSubject)
    {
        //
    }
}
