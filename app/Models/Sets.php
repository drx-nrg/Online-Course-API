<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sets extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "sets";

    public function lessons(){
        return $this->hasMany(Lessons::class, "set_id", "id");
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
