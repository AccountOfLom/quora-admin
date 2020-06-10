<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    public function getCreatedAtAttribute($value)
    {
        return $value ? date('Y-m-d H:i', strtotime($value)) : '';
    }
}
