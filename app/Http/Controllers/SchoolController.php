<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Http\Services\SchoolService;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Http\JsonResponse;

class SchoolController extends Controller
{
    public function __construct(protected SchoolService $schoolService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->schoolService->getAll();
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
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        $result = $this->schoolService->saveSchool($request->validated());
        return response()->json([
            'message' => 'Colegio registrado éxitosamente.',
            'errors' => [],
            'data' => [
                'school' => $result['school']
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($school)
    {
        $result = $this->schoolService->showSchool($school);
        return response()->json([
            'message' => 'Colegio encontrado.',
            'errors'=> [],
            'data' => [
                'school' => $result
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        $result = $this->schoolService->updateSchool($request->validated(), $school);
        return response()->json([
            'message' => 'Colegio actualizado éxitosamente.',
            'errors'=> [],
            'data' => [
                'school' => $result
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($school)
    {
        $result = $this->schoolService->deleteSchool($school);
        return response()->json([
            'message' => 'Colegio eliminado éxitosamente.',
            'errors'=> [],
            'data' => [
                'school' => $result
            ]
        ]);
    }
}
