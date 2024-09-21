<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonContents extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    protected $appends = ["options"];
    
    public function lessons(){
        return $this->belongsTo(Lessons::class, "lesson_id", "id");
    }

    public function options(){
        return $this->hasMany(Options::class, "lesson_content_id", "id");
    }

    public function getOptionsAttribute(){
        return $this->options()->get();
    }
}
