<?php


namespace App\Admin\Repositories;


use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\Topics as TopicsModel;


class Topics extends EloquentRepository
{
    protected $eloquentClass = TopicsModel::class;

}
