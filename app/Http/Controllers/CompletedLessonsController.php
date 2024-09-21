<?php

namespace App\Http\Controllers;

use App\Models\CompletedLessons;
use App\Models\Enrollments;
use App\Models\Lessons;
use App\Models\Sets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompletedLessonsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard("user")->user();
        if(is_null($user)) return $this->forbidden();

        $user_courses = Enrollments::with("course")->where('user_id', $user->id)->get();
        $courses = [];
        $index = 0;
        foreach($user_courses as $course){
            $courses[$index]["course"] = $course->course;
            $index++;
        }

        for($i = 0; $i < count($courses); $i++){
            $sets_course_ids = Sets::with(["lessons"])->where('course_id', $courses[$i]["course"]->id)->get()->pluck("id");
            $lessons_ids = Lessons::whereIn('set_id', $sets_course_ids)->get()->pluck("id");
            $lesson_completed_ids = CompletedLessons::where('user_id', $user->id)->whereIn('lesson_id', $lessons_ids)->get()->pluck("lesson_id");
            $courses[$i]["completed_lessons"] = Lessons::whereIn('id', $lesson_completed_ids)->get(["id","name","order"]);
        }

        return response()->json([
            "status" => "success",
            "message" => "User progress retrieved successfully",
            "data" => [
                "progress" => $courses
            ]
        ], 201);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CompletedLessons $completedLessons)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompletedLessons $completedLessons)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompletedLessons $completedLessons)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompletedLessons $completedLessons)
    {
        //
    }
}
