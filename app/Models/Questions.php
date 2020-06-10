<?php


namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Questions extends Base
{
    use SoftDeletes;

    public function answers()
    {
        return $this->hasMany(Answers::class, 'question_id', 'id');
    }
}
