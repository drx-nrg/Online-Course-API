<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Sets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SetsController extends Controller
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
    public function store(Request $request, Courses $courses)
    {
        if(is_null(Auth::guard("admin")->user())){
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access Forbidden"
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $data = $request->all();
        $latest_set_order = Sets::where('course_id', $courses->id)->latest()->first();
        $data["course_id"] = $courses->id;
        $data["order"] = is_null($latest_set_order) ? 0 : $latest_set_order->order + 1;

        $set = Sets::create($data);

        return response()->json([
            "status" => "success",
            "message" => "Set successfully added",
            "data" => $set,
        ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(Sets $sets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sets $sets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sets $sets)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courses $courses, $set_id)
    {
        if(is_null(Auth::guard("admin")->user())){
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access Forbidden"
            ], 403);
        }

        $set = Sets::where('course_id', $courses->id)->where('id', $set_id)->first();

        if(is_null($set)){
            return $this->notfound();
        }

        $set->delete();
        return response()->json([
            "status" => "success",
            "message" => "Set succesfully deleted"
        ], 200);
    }
}
