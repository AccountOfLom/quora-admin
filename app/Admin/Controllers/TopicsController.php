<?php


namespace App\Admin\Controllers;


use App\Admin\Repositories\Topics;
use App\Console\Commends\TranslateAnswers;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;


/**
 * 话题
 * Class TopicsController
 * @package App\Admin\Controllers
 */
class TopicsController extends AdminController
{

    public function index(Content $content)
    {
        (new TranslateAnswers())->handle();
        return $content
            ->header('话题')
            ->body($this->grid());
    }

    protected function grid()
    {
        return Grid::make(new Topics(), function (Grid $grid) {
            $grid->quickSearch(['topic', 'topic_cn']);
            $grid->id('ID')->sortable();
            $grid->topic('话题');
            $grid->topic_cn('话题 CN');
            $grid->status('爬取？')->switch();
            $grid->created_at;
        });
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('话题')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('话题')
            ->body($this->form());
    }

    public function show($id, Content $content)
    {
        return $content->header('话题')
            ->body(Show::make($id, new Topics(), function (Show $show) {
                $show->id('ID');
                $show->topic('话题');
                $show->topic_cn('话题 CN');
                $show->statue('爬取 ?')->as(function ($statue) {
                    return $statue ? '是' : '否';
                });
                $show->created_at();
                $show->updated_at();
            }));
    }

    protected function form()
    {
        $form = Form::make(new Topics(), function (Form $form) {
            $form->display('id', 'ID');
            $form->text('topic', '话题');
            $form->text('topic_cn', '话题 CN');
            $form->switch('status', '爬取 ？');
        });
        return $form;
    }
}
