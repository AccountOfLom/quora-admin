<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //话题
    $router->resource('topics', TopicsController::class);

    //问题
    $router->resource('questions', QuestionsController::class);

    //回答
    $router->resource('answers', AnswersController::class);

    //回答详情
    $router->get('answer-details/{questionID}', 'AnswersController@wxHtml');

});
