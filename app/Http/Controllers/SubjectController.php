<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Services\SubjectService;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function __construct(protected SubjectService $subjectService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        return $this->subjectService->store($validated, $user);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        //
    }
}
