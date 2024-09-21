<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Enrollments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Courses::latest()->get();
        return response()->json([
            "status" => "success",
            "message" => "Courses retrieved successfully",
            "data" => [
                "courses" => $courses
            ]
        ], 200);
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
        if(is_null(Auth::guard("admin")->user())){
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access Forbidden"
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => ["nullable"],
            "slug" => ["required", "unique:courses,slug"],
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $course = Courses::create($request->all());
        return response()->json([
            "status" => "success",
            "message" => "Course successfully added",
            "data" => $course
        ], 201);
    }   

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $course = Courses::with(['sets' => function($set) {
            $set->with(['lessons' => function($lesson) {
                $lesson->with(['contents'])->orderBy('order', 'asc')->get();
            }])->orderBy('order', 'asc')->get();
        }])->whereSlug($slug)->first();

        return response()->json([
            "status" => "success",
            "message" => "Course detail retrieved successfully",
            "data" => $course
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Courses $courses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        if(is_null(Auth::guard("admin")->user())){
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access Forbidden"
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => ["nullable"],
            "is_published" => ["nullable", "boolean"]
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $course = Courses::whereSlug($slug)->first();

        if(is_null($course)){
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $course->update($request->all());
        return response()->json([
            "status" => "success",
            "message" => "Course successfully updated",
            "data" => Courses::whereSlug($slug)->first()
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        if(is_null(Auth::guard("admin")->user())) 
            return $this->forbidden();
        

        $course = Courses::whereSlug($slug);
        if(is_null($course)){
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $course->delete();

        return response()->json([
            "status" => "success",
            "message" => "Course successfully deleted ",
        ], 200);
    }

    public function register_course(Request $request, $course_slug)
    {
        if(is_null(Auth::guard("user")->user())) 
            return $this->forbidden();

        $user = Auth::guard("user")->user();

        $course = Courses::whereSlug($course_slug)->first();
        if(is_null($course)){
            return $this->notfound();
        }

        $enrollments = Enrollments::where("user_id", $user->id)->where("course_id", $course->id)->first();

        if(!is_null($enrollments)){
            return response()->json([
                "status" => "error",
                "message" => "The user is already registered for this course"
            ], 400);
        }

        Enrollments::create([
            "user_id" => Auth::guard("user")->user()->id,
            "course_id" => $course->id
        ]);

        return response()->json([
            "status" => "success",
            "message" => "User registered successful"
        ], 201);
    }
}
