<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 话题
 * Class Topics
 * @package App\Models
 */
class Topics extends Model
{
    use SoftDeletes;
}
