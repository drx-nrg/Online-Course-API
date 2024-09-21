<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function contents(){
        return $this->belongsTo(LessonContents::class, "lesson_content_id", "id");
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
