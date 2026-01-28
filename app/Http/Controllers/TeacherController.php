<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Http\Services\TeacherService;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function __construct(protected TeacherService $teacherService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return $this->teacherService->getAll($user);
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
    public function store(StoreTeacherRequest $request)
    {
        $validated = $request->validated();
        $userAuth = Auth::user();
        return $this->teacherService->saveTeacher($validated, $userAuth);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $teacher)
    {
        $user = Auth::user();
        return $this->teacherService->showTeacher($teacher, $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, int $teacher)
    {
        $validated = $request->validated();
        $user = Auth::user();
        return $this->teacherService->updateTeacher($validated,  $teacher, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $teacher)
    {
        $user = Auth::user();
        return $this->teacherService->deleteTeacher($teacher, $user);
    }
}
