<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lessons extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sets(){
        return $this->belongsTo(Sets::class, "set_id", "id");
    }

    public function contents(){
        return $this->hasMany(LessonContents::class, "lesson_id", "id");
    }



    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
