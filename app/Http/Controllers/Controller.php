<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function createApi($data, $status)
    {
        return response()->json($data, $status);
    }

    public function validateFails($errors)
    {
        return response()->json([
            "status" => "error",
            "message" => "Invalid field(s) in request",
            "errors" => $errors
        ], 400);
    }

    public function notfound(){
        return response()->json([
            "status" => "not_found",
            "message" => "Resource not found"
        ], 404);
    }

    public function forbidden()
    {
        return response()->json([
            "status" => "insufficient_permissions",
            "message" => "Access Forbidden"
        ], 403);
    }
}
