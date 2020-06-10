<?php


namespace App\Admin\Repositories;


use Dcat\Admin\Repositories\EloquentRepository;

class Questions extends EloquentRepository
{
    protected $eloquentClass = \App\Models\Questions::class;

}
