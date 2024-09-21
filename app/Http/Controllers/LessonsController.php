<?php

namespace App\Http\Controllers;

use App\Models\LessonContents;
use App\Models\Lessons;
use App\Models\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LessonsController extends Controller
{
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
            "set_id" => ["required", "exists:sets,id"],
            "contents" => ["required", "array"],
            "contents.*.type" => ["required", "in:learn,quiz"],
            "contents.*.content" => ["required","string"],
            "contents.*.options" => ["required_if:contents.*.type,quiz","array"],
            "contents.*.options.*.option_text" => ["required_if:contents.*.type,quiz","string"],
            "contents.*.options.*.is_correct" => ["required_if:contents.*.type,quiz","boolean"]
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $last_lesson_order = Lessons::where("set_id", $request->set_id)->latest()->first();
        $last_lesson_order = is_null($last_lesson_order) ? 0 : $last_lesson_order->order + 1;
        
        $lesson_data = $request->only("set_id", "name", "order");
        $lesson_data["order"] = $last_lesson_order;
        $lesson = Lessons::create($lesson_data);

        $contents = $request->contents;
        $lesson_content_order = -1;
        foreach($contents as $content){
            $lesson_content = LessonContents::create([
                "lesson_id" => $lesson->id,
                "type" => $content["type"],
                "content" => $content["content"],
                "order" =>  $lesson_content_order + 1
            ]);

            if($content["type"] == "quiz"){
                foreach($content["options"] as $option){
                    Options::create([
                        "lesson_content_id" => $lesson_content->id,
                        "option_text" => $option["option_text"],
                        "is_correct" => boolval($option["is_correct"])
                    ]);
                }
            }

            $lesson_content_order = $lesson_content_order + 1;
        }

        unset($lesson["set_id"]);

        return response()->json([
            "status" => "success",
            "message" => "Lesson successfully added",
            "data" => $lesson
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Lessons $lessons)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lessons $lessons)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lessons $lessons)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lesson_id)
    {   
        if(is_null(Auth::guard("admin")->user())){
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access Forbidden"
            ], 403);
        }

        $lesson = Lessons::find($lesson_id);

        if(is_null($lesson)){
            return $this->notfound();
        }

        $lesson->delete();
        return response()->json([
            "status" => "success",
            "message" => "Lesson successfully deleted"
        ], 200);
    }

    public function checkAnswer(Request $request, $lesson_id, $content_id)
    {
        if(is_null(Auth::guard("user")->user())) 
            return $this->forbidden();

        $lesson = Lessons::find($lesson_id);
        if(is_null($lesson)){
            return $this->notfound();
        }

        $lesson_content = LessonContents::find($content_id);
        if(is_null($lesson_content)){
            return $this->notfound();
        }

        if($lesson_content->type != "quiz"){
            return response()->json([
                "status" => "error",
                "message" => "Only for quiz content"
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            "option_id" => ["required","exists:options,id"]
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $option = Options::find($request->option_id);
        if(is_null($option)){
            return $this->notfound();
        }

        $data = [
            "question" => $lesson_content->content,
            "user_answer" => $option->option_text,
            "is_correct" => $option->is_correct ? true : false
        ];

        return response()->json([
            "status" => "success",
            "message" => "Check answer success",
            "data" => $data
        ], 200);
    }   
}
