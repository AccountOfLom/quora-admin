<?php


namespace App\Admin\Controllers;

use App\Admin\Repositories\Questions;
use App\Models\Topics;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;


/**
 * 问题
 * Class QuestionsController
 * @package App\Admin\Controllers
 */
class QuestionsController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('问题')
            ->body($this->grid());
    }

    protected function grid()
    {
        return Grid::make(new Questions(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->quickSearch(['text', 'text_cn']);
            $grid->id('ID')->sortable();
            $grid->topic('话题')->display(function ($topic) {
                return Topics::where('topic', $topic)->value('topic_cn');
            });
            $grid->text('问题');
            $grid->text_cn('问题 CN');
            $grid->link('Quora地址')->display(function ($link) {
                return "<a href='https://www.quora.com{$link}' target='_blank' >www.quora.com{$link}</a>";
            });
            $grid->status('前台显示？')->switch();
            $grid->article_released('已发布到公众号？')->switch();
            $grid->revised('已修正翻译结果？')->switch();
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
            ->body(Show::make($id, new Questions(), function (Show $show) {
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
        $form = Form::make(new Questions(), function (Form $form) {
            $form->display('id', 'ID');
            $form->text('topic', '话题');
            $form->text('topic_cn', '话题 CN');
            $form->switch('status', '爬取 ？');
        });
        return $form;
    }
}
