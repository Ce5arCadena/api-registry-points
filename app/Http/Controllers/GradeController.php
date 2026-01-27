<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Http\Services\GradeService;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function __construct(protected GradeService $gradeService) {}

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
    public function store(StoreGradeRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        return $this->gradeService->store($validated, $user);
    }

    /**
     * Display the specified resource.
     */
    public function show($grade)
    {
        $user = Auth::user();
        return $this->gradeService->showGrade($grade, $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreGradeRequest $request, $grade)
    {
        $validated = $request->validated();
        $user = Auth::user();

        return $this->gradeService->update($validated, $grade, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $grade)
    {
        $user = Auth::user();
        return $this->gradeService->destroy($grade, $user);
    }
}
